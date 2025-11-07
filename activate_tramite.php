<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tramite;

$tramite = Tramite::find(9);
$tramite->is_active = true;
$tramite->save();

echo "✅ Trámite ID 9 activado exitosamente!\n";
echo "URL para probar: http://127.0.0.1:8000/solicitudes/create/9\n";