/**
 * Main application JavaScript entry point
 * This file is application's JS entry used by Vite. Keep it minimal: import
 */

import $ from 'jquery';
window.$ = window.jQuery = $;

import 'jquery-validation';

import Swal from 'sweetalert2';
window.Swal = Swal;

import 'bootstrap';
import 'admin-lte';

import './helper.js';