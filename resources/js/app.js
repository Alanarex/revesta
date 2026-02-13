/**
 * Main application JavaScript entry point
 * This file is the application's JS entry used by Vite.
 * Keep it minimal and import all necessary libraries here.
 */

/* -------------------- jQuery -------------------- */
import $ from 'jquery';
window.$ = window.jQuery = $;

/* -------------------- jQuery Validation -------------------- */
import 'jquery-validation';

/* -------------------- SweetAlert2 -------------------- */
import Swal from 'sweetalert2';
window.Swal = Swal;

/* -------------------- AdminLTE 4 -------------------- */
/* AdminLTE 4 includes Bootstrap 5 - no need to import separately */
import 'admin-lte';

/* -------------------- Bootstrap JS -------------------- */
import 'bootstrap';

/* -------------------- DataTables -------------------- */
import DataTable from 'datatables.net-bs5';
window.DataTable = DataTable;

/* -------------------- Custom helper JS -------------------- */
import './helper.js';           // Your own helper functions
import './validation.js';       // Global French/custom browser validation messages
