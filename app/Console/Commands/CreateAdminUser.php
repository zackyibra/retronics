<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create
                            {--email= : Alamat email administrator}
                            {--name= : Nama lengkap administrator}
                            {--password= : Kata sandi (jika kosong, akan dibuatkan otomatis secara acak dan kuat)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat atau memperbarui akun Administrator Retronics secara aman.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('========================================================');
        $this->info('      RETRONICS - SETUP AKUN ADMINISTRATOR AMAN       ');
        $this->info('========================================================');

        $email = $this->option('email') ?: $this->ask('Masukkan Email Administrator');

        if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid! Proses dibatalkan.');

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $this->warn("Pengguna dengan email [{$email}] sudah terdaftar di sistem.");
            if (! $this->confirm('Apakah Anda ingin memperbarui akun ini menjadi Administrator dan menyetel ulang kata sandinya?', true)) {
                $this->info('Operasi dibatalkan.');

                return self::SUCCESS;
            }
            $name = $this->option('name') ?: $user->name;
        } else {
            $name = $this->option('name') ?: $this->ask('Masukkan Nama Lengkap Administrator', 'Admin Retronics Store');
        }

        $rawPassword = $this->option('password');
        $isGenerated = false;

        if (empty($rawPassword)) {
            $rawPassword = Str::password(16);
            $isGenerated = true;
        }

        if ($user) {
            $user->update([
                'name' => $name,
                'role' => 'admin',
                'password' => Hash::make($rawPassword),
            ]);
            $this->info('✓ Akun administrator berhasil diperbarui.');
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'role' => 'admin',
                'phone' => '081929513780',
                'address' => 'Store Retronics Gunung Putri, Bogor',
                'password' => Hash::make($rawPassword),
            ]);
            $this->info('✓ Akun administrator baru berhasil dibuat.');
        }

        $this->newLine();
        $this->table(
            ['Parameter', 'Keterangan'],
            [
                ['Peran (Role)', 'Administrator'],
                ['Nama', $user->name],
                ['Email', $user->email],
                ['Kata Sandi '.($isGenerated ? 'Sementara (Acak)' : 'Baru'), $rawPassword],
                ['Status Hashing', 'Bcrypt / Argon2 (Hash::make)'],
                ['URL Portal Admin', url('/admin/login')],
            ]
        );

        $this->newLine();
        $this->warn('PENTING:');
        $this->warn('1. Salin dan simpan kata sandi di atas pada password manager yang aman.');
        $this->warn('2. Kata sandi tidak dicatat pada file log maupun kode sumber demi keamanan.');
        $this->warn('3. Segera ganti kata sandi setelah berhasil login ke panel admin.');
        $this->info('========================================================');

        return self::SUCCESS;
    }
}
