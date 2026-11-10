<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Sin sesión, la raíz redirige al login.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    /**
     * El login es accesible sin sesión.
     */
    public function test_login_page_is_visible_for_guests(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    /**
     * Las rutas protegidas exigen autenticación.
     */
    public function test_protected_routes_require_authentication(): void
    {
        foreach (['/', '/productos', '/stock', '/pedidos', '/ventas', '/clientes', '/reportes'] as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }
}
