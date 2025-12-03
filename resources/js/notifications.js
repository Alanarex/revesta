// function loadNotifications() {
//     $.get('/notifications', function (response) {
//         if (response.success) {
//             updateNotificationBadge();
//             // Additional notification rendering logic can go here
//         }
//     });
// }

// function updateNotificationBadge() {
//     $.get('/notifications/unread-count', function (response) {
//         if (response.success) {
//             const badge = $('#notificationBadge');
//             if (response.count > 0) {
//                 badge.text(response.count).show();
//             } else {
//                 badge.hide();
//             }
//         }
//     });
// }

// $(document).on('click', '#markAllReadBtn', function () {
//     $.post('/notifications/mark-all-read', {
//         _token: $('meta[name="csrf-token"]').attr('content')
//     }, function (response) {
//         if (response.success) {
//             updateNotificationBadge();
//             Swal.fire({
//                 icon: 'success',
//                 title: response.message,
//                 timer: 1500,
//                 showConfirmButton: false
//             });
//         }
//     });
// });

// // Load notifications on page load
// $(function () {
//     updateNotificationBadge();
//     setInterval(updateNotificationBadge, 30000); // Update every 30 seconds
// });