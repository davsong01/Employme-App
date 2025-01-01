<?php

use App\Models\User;
use App\Models\Admin;
use App\Models\FacilitatorTraining;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->json('menu_permissions')->nullable();
            $table->string('email', 191)->unique();
            $table->json('metadata')->nullable();
            $table->string('phone', 191)->nullable();
            $table->string('license', 255)->nullable();
            $table->string('waacsp_url', 255)->nullable();
            $table->string('password', 191);
            $table->json('roles', 255)->nullable();
            $table->string('profile_picture', 191)->default('avatar.jpg')->nullable();
            $table->string('gender', 191)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->text('profile')->nullable();
            $table->string('payment_mode', 11)->nullable();
            $table->float('earnings')->nullable();
            $table->string('status', 15)->nullable();
            $table->timestamp('last_login')->nullable();
            $table->integer('off_season_availability')->nullable();
            $table->timestamps();
        });

        $admins = User::where('role_id', '!=', 'Student')->get();

        if ($admins) {
            foreach ($admins as $admin) {
                try {
                    //code...
                    $newAdmin = Admin::updateOrCreate(['email' => $admin->email], [
                        'name' => $admin->name,
                        'menu_permissions' => $admin->menu_permissions,
                        'email' => $admin->email,
                        'metadata' => $admin->metadata,
                        'phone' => $admin->t_phone ?? null,
                        'license' => $admin->license,
                        'waacsp_url' => $admin->waacsp_url,
                        'password' => $admin->password,
                        'roles' => isset($admin->role_id) ? explode(',', $admin->role_id) : null,
                        'profile_picture' => $admin->profile_picture,
                        'gender' => $admin->gender,
                        'remember_token' => $admin->remember_token,
                        'profile' => $admin->profile,
                        'payment_mode' => $admin->payment_mode,
                        'earnings' => $admin->earnings,
                        'status' => $admin->status,
                        'last_login' => $admin->last_login,
                        'off_season_availability' => $admin->off_season_availability
                    ]);

                    FacilitatorTraining::where('user_id', $admin->id)->update([
                        'user_id' => $newAdmin->id,
                    ]);

                    $admin->delete();

                } catch (\Throwable $th) {
                    //throw $th;
                    dd($th->getMessage(), $admin);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
