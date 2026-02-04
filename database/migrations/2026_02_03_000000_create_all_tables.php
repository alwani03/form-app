<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('department_name');
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role')->index('roles_index_role');
            $table->string('description')->nullable();
            $table->integer('is_active')->default(1)->index('roles_index_is_active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique()->index('users_index_username');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->integer('last_login')->nullable();
            $table->integer('is_active')->default(1)->index('users_index_is_active');
            $table->foreignId('department_id')->constrained('departments');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index('menus_index_name');
            $table->string('description')->nullable();
            $table->string('url')->index('menus_index_url');
            $table->integer('is_active')->default(1)->index('menus_index_is_active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('role_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->index('role_menus_index_role_id');
            $table->foreignId('menu_id')->constrained('menus')->index('role_menus_index_menu_id');
            $table->integer('is_active')->default(1)->index('role_menus_index_is_active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('log_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('remark');
            $table->timestamps();
        });

        Schema::create('document_type_configs', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('document_name');
            $table->boolean('approval')->default(false);
            $table->json('setting')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('form_requests', function (Blueprint $table) {
            $table->id();
            $table->string('form_no');
            $table->string('form_name');
            $table->enum('status', ['pending', 'process', 'finished', 'reject'])->default('pending');
            $table->foreignId('document_type_id')->constrained('document_type_configs');
            $table->json('type')->nullable(); // stored as json because user input said type string []
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('incident_form_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->integer('form_id');
            $table->string('report_no', 30);
            $table->integer('destination_group')->nullable();
            $table->timestamp('incident_date')->nullable();
            $table->text('incident_desc')->nullable();
            $table->integer('incident_type')->comment('1 : insiden umum , 2 : insiden external');
            $table->text('impact_description')->nullable();
            $table->integer('pic_user_id')->nullable();
            $table->text('incident_root_cause')->nullable();
            $table->text('action_plan')->nullable();
            $table->text('incident_resolution')->nullable();
            $table->text('incident_status')->comment('01 : closed, 02: Progress, 03: Not Closed');
            $table->integer('approve_signature_id_department')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('summary')->nullable();
            $table->text('chronology')->nullable();
            $table->text('actions_taken')->nullable();
            $table->text('conclusion')->nullable();
            $table->string('status_remarks', 255)->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->integer('is_deleted')->default(0)->comment('1 : hapus, 0 : tidak di hapus');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('log_form_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained('form_requests');
            $table->string('status'); // enum according to FormRequest status
            $table->integer('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_form_requests');
        Schema::dropIfExists('incident_form_details');
        Schema::dropIfExists('form_requests');
        Schema::dropIfExists('document_type_configs');
        Schema::dropIfExists('log_activities');
        Schema::dropIfExists('role_menus');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('departments');
    }
};
