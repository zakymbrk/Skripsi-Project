<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration controller for CLI usage.
 * Run with: php index.php migrate
 */
class Migrate extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // Ensure this is called from CLI for security.
        if (!$this->input->is_cli_request()) {
            show_error('Direct access not allowed');
        }
        $this->load->library('migration');
    }

    public function index() {
        // Apply latest migrations
        if ($this->migration->latest() === FALSE) {
            echo "Migration error: " . $this->migration->error_string() . "\n";
            exit(1);
        }
        echo "Migrations applied successfully.\n";
    }
}
?>
