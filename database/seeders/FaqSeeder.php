<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'pregunta' => '¿Cómo puedo iniciar un nuevo trámite?',
                'respuesta' => 'Para iniciar un nuevo trámite, debes ir a la sección "Trámites" en el menú lateral, seleccionar el tipo de trámite que necesitas y hacer clic en "Iniciar Trámite". Sigue las instrucciones para completar el formulario correspondiente.',
                'categoria' => 'Trámites',
                'orden' => 1,
                'activo' => true
            ],
            [
                'pregunta' => '¿Cómo puedo verificar el estado de mi solicitud?',
                'respuesta' => 'Para verificar el estado de tu solicitud, ve a la sección "Solicitudes" en el menú lateral. Allí encontrarás una lista de todas tus solicitudes con su estado actual (pendiente, aprobado o rechazado).',
                'categoria' => 'Solicitudes',
                'orden' => 2,
                'activo' => true
            ],
            [
                'pregunta' => '¿Qué documentos necesito para hacer un trámite?',
                'respuesta' => 'Los documentos requeridos varían según el tipo de trámite. Al seleccionar un trámite específico, se te informará qué documentos necesitas adjuntar. Generalmente, se requiere tu documento de identidad vigente y, dependiendo del trámite, fotografías o documentación adicional.',
                'categoria' => 'Documentación',
                'orden' => 3,
                'activo' => true
            ],
            [
                'pregunta' => '¿Cómo puedo recuperar mi contraseña?',
                'respuesta' => 'Si olvidaste tu contraseña, haz clic en "¿Olvidaste tu contraseña?" en la página de inicio de sesión. Se te enviará un correo electrónico con instrucciones para restablecer tu contraseña.',
                'categoria' => 'Cuenta',
                'orden' => 4,
                'activo' => true
            ],
            [
                'pregunta' => '¿Cuánto tiempo tarda en procesarse mi solicitud?',
                'respuesta' => 'El tiempo de procesamiento varía según el tipo de trámite y la carga actual de trabajo. Por lo general, las solicitudes se procesan en un plazo de 3 a 5 días hábiles. Puedes verificar el estado de tu solicitud en cualquier momento en la sección "Solicitudes".',
                'categoria' => 'Proceso',
                'orden' => 5,
                'activo' => true
            ],
            [
                'pregunta' => '¿Qué hago si mi solicitud fue rechazada?',
                'respuesta' => 'Si tu solicitud fue rechazada, podrás ver el motivo del rechazo en los comentarios de la solicitud. Puedes corregir los errores señalados y volver a presentar una nueva solicitud.',
                'categoria' => 'Solicitudes',
                'orden' => 6,
                'activo' => true
            ],
            [
                'pregunta' => '¿Cómo puedo contactar con soporte técnico?',
                'respuesta' => 'Para contactar con soporte técnico, puedes escribir un correo a soporte@muniapp.com o llamar al número 0123-456-789 en horario de oficina (8:00 AM - 4:00 PM, de lunes a viernes).',
                'categoria' => 'Ayuda',
                'orden' => 7,
                'activo' => true
            ],
            [
                'pregunta' => '¿Puedo cancelar una solicitud en curso?',
                'respuesta' => 'No es posible cancelar una solicitud una vez enviada. Si necesitas realizar cambios o cancelar un trámite, deberás contactar directamente con la oficina municipal correspondiente.',
                'categoria' => 'Solicitudes',
                'orden' => 8,
                'activo' => true
            ],
            [
                'pregunta' => '¿La aplicación funciona en dispositivos móviles?',
                'respuesta' => 'Sí, MuniApp está diseñada para funcionar correctamente en dispositivos móviles. Puedes acceder desde cualquier navegador en tu smartphone o tablet.',
                'categoria' => 'Técnico',
                'orden' => 9,
                'activo' => true
            ],
            [
                'pregunta' => '¿Cómo puedo actualizar mis datos personales?',
                'respuesta' => 'Para actualizar tus datos personales, ve a la sección "Mi Perfil" en el menú de usuario (parte superior derecha). Allí podrás modificar tu información personal, cambiar tu contraseña o actualizar tu correo electrónico.',
                'categoria' => 'Cuenta',
                'orden' => 10,
                'activo' => true
            ],
        ];
        
        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
