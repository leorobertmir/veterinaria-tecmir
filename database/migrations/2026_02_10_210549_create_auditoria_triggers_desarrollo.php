<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. CREAR LA TABLA tb_auditoria DENTRO DEL ESQUEMA 'desarrollo'
        // Usamos desarrollo.tb_auditoria para asegurar que quede junto a tus otras tablas
        if (!Schema::hasTable('desarrollo.tb_auditoria')) {
            Schema::create('desarrollo.tb_auditoria', function (Blueprint $table) {
                $table->id('audi_id'); 
                $table->timestamp('audi_date')->useCurrent();
                $table->string('audi_user', 30); 
                $table->string('audi_table', 100);
                $table->string('audi_sql_type', 3); // INS, UPD, DEL
                $table->jsonb('audi_old_record')->nullable();
                $table->jsonb('audi_new_record')->nullable();
            });

            // Agregamos la validación CHECK manualmente
            DB::statement("ALTER TABLE desarrollo.tb_auditoria ADD CONSTRAINT tb_auditoria_audi_sql_type_check CHECK (audi_sql_type IN ('INS', 'UPD', 'DEL'))");
        }

        // 2. CREAR LA FUNCIÓN DENTRO DEL ESQUEMA 'desarrollo'
        // Esto es vital: definimos la función como desarrollo.fn_control_auditoria
        DB::unprepared("
            CREATE OR REPLACE FUNCTION desarrollo.fn_control_auditoria()
            RETURNS trigger AS $$
            DECLARE
                v_sql_type VARCHAR(3);
                v_user VARCHAR(30);
            BEGIN
                v_user := current_user;

                IF TG_OP = 'INSERT' THEN
                    v_sql_type := 'INS';
                    INSERT INTO desarrollo.tb_auditoria(audi_date, audi_user, audi_table, audi_sql_type, audi_old_record, audi_new_record)
                    VALUES(now(), v_user, TG_TABLE_SCHEMA||'.'||TG_TABLE_NAME, v_sql_type, NULL, to_jsonb(NEW));
                    RETURN NEW;

                ELSIF TG_OP = 'UPDATE' THEN
                    v_sql_type := 'UPD';
                    INSERT INTO desarrollo.tb_auditoria(audi_date, audi_user, audi_table, audi_sql_type, audi_old_record, audi_new_record)
                    VALUES(now(), v_user, TG_TABLE_SCHEMA||'.'||TG_TABLE_NAME, v_sql_type, to_jsonb(OLD), to_jsonb(NEW));
                    RETURN NEW;

                ELSIF TG_OP = 'DELETE' THEN
                    v_sql_type := 'DEL';
                    INSERT INTO desarrollo.tb_auditoria(audi_date, audi_user, audi_table, audi_sql_type, audi_old_record, audi_new_record)
                    VALUES(now(), v_user, TG_TABLE_SCHEMA||'.'||TG_TABLE_NAME, v_sql_type, to_jsonb(OLD), NULL);
                    RETURN OLD;
                END IF;
                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // 3. ACTIVAR LOS TRIGGERS EN TUS TABLAS DE 'desarrollo'
        // Listamos las tablas importantes que ya tienes creadas en el esquema desarrollo
        $tablas = ['clientes', 'productos', 'mascotas', 'facturas', 'users'];

        foreach ($tablas as $tabla) {
            // Borramos trigger previo si existe para evitar errores
            DB::unprepared("DROP TRIGGER IF EXISTS trg_auditoria ON desarrollo.{$tabla}");
            
            // Creamos el trigger apuntando explícitamente a la tabla en desarrollo
            DB::unprepared("
                CREATE TRIGGER trg_auditoria
                AFTER INSERT OR UPDATE OR DELETE ON desarrollo.{$tabla}
                FOR EACH ROW EXECUTE FUNCTION desarrollo.fn_control_auditoria();
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tablas = ['clientes', 'productos', 'mascotas', 'facturas', 'users'];
        foreach ($tablas as $tabla) {
            DB::unprepared("DROP TRIGGER IF EXISTS trg_auditoria ON desarrollo.{$tabla}");
        }
        
        DB::unprepared("DROP FUNCTION IF EXISTS desarrollo.fn_control_auditoria");
        Schema::dropIfExists('desarrollo.tb_auditoria');
    }
};
