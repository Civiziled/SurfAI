<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ThemeToggleTest extends DuskTestCase
{
    /**
     * Test theme toggle functionality and persistence.
     */
    public function testThemeToggleAndPersistence(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->waitFor('button[title="Toggle Dark Mode"]')
                    // Initial state check (assuming default is light or system)
                    // We click to ensure we are in a known state if needed, but let's just toggle.
                    
                    // Toggle to Dark
                    ->click('button[title="Toggle Dark Mode"]')
                    ->pause(500) // Wait for transition
                    ->script('return document.documentElement.classList.contains("dark")')[0];
            
            // Verify Dark Mode is active
            $isDark = $browser->script('return document.documentElement.classList.contains("dark")')[0];
            $this->assertTrue($isDark, 'Failed to switch to dark mode.');

            // Reload to check persistence
            $browser->refresh()
                    ->pause(500);

            $isDarkAfterReload = $browser->script('return document.documentElement.classList.contains("dark")')[0];
            $this->assertTrue($isDarkAfterReload, 'Dark mode did not persist after reload.');

             // Toggle back to Light
             $browser->click('button[title="Toggle Dark Mode"]')
                     ->pause(500);
            
             $isLight = $browser->script('return !document.documentElement.classList.contains("dark")')[0];
             $this->assertTrue($isLight, 'Failed to switch back to light mode.');
        });
    }
}
