<?php

// Update Program Migration
$progMigration = 'database/migrations/2026_08_14_041621_create_programs_table.php';
$progMigrationContent = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('satuan')->nullable();
            $table->integer('target')->default(0);
            $table->decimal('alokasi', 20, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
PHP;
file_put_contents($progMigration, $progMigrationContent);

// Update Kegiatan Migration
$kegMigration = 'database/migrations/2026_08_14_041621_create_kegiatans_table.php';
$kegMigrationContent = <<<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->string('nama');
            $table->integer('volume_target')->default(0);
            $table->integer('volume_realisasi')->default(0);
            $table->decimal('anggaran_alokasi', 20, 2)->default(0);
            $table->decimal('anggaran_realisasi', 20, 2)->default(0);
            $table->string('pelaksanaan')->nullable();
            $table->json('kelengkapan_bulanan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
PHP;
file_put_contents($kegMigration, $kegMigrationContent);

// Update Program Model
$progModel = 'app/Models/Program.php';
$progModelContent = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'target',
        'alokasi',
    ];

    protected $appends = [
        'realisasi_target',
        'realisasi_alokasi',
    ];

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }

    public function getRealisasiTargetAttribute()
    {
        return $this->kegiatans()->sum('volume_realisasi');
    }

    public function getRealisasiAlokasiAttribute()
    {
        return $this->kegiatans()->sum('anggaran_realisasi');
    }
}
PHP;
file_put_contents($progModel, $progModelContent);

// Update Kegiatan Model
$kegModel = 'app/Models/Kegiatan.php';
$kegModelContent = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'nama',
        'volume_target',
        'volume_realisasi',
        'anggaran_alokasi',
        'anggaran_realisasi',
        'pelaksanaan',
        'kelengkapan_bulanan',
    ];

    protected $casts = [
        'kelengkapan_bulanan' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
PHP;
file_put_contents($kegModel, $kegModelContent);

echo "Migrations and Models updated.\n";
?>
