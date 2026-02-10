import $ from 'jquery';
import Swal from 'sweetalert2';
import { getCsrfToken } from '../../../utils';

$(function () {
    const csrfToken = getCsrfToken();

    // Send now button
    $(document).on('click', '.send-now-btn', function (e) {
        e.preventDefault();
        const campaignId = $(this).data('campaign-id');
        
        Swal.fire({
            title: 'Envoyer la campagne',
            text: 'Êtes-vous sûr de vouloir envoyer cette campagne à présent?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, envoyer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = $('<form>')
                    .attr('method', 'POST')
                    .attr('action', `/admin/newsletters/${campaignId}/send-now`)
                    .append($('<input>').attr('type', 'hidden').attr('name', '_token').val(csrfToken));
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Delete campaign button
    $(document).on('click', '.delete-campaign-btn', function (e) {
        e.preventDefault();
        const campaignId = $(this).data('campaign-id');
        const campaignTitle = $(this).data('campaign-title');
        
        Swal.fire({
            title: 'Supprimer cette campagne?',
            text: `Êtes-vous sûr de vouloir supprimer la campagne "${campaignTitle}"? Cette action ne peut pas être annulée.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = $('<form>')
                    .attr('method', 'POST')
                    .attr('action', `/admin/newsletters/${campaignId}`)
                    .append($('<input>').attr('type', 'hidden').attr('name', '_token').val(csrfToken))
                    .append($('<input>').attr('type', 'hidden').attr('name', '_method').val('DELETE'));
                
                $('body').append(form);
                form.submit();
            }
        });
    });

    // Cancel schedule button
    $(document).on('click', '.cancel-schedule-btn', function (e) {
        e.preventDefault();
        const campaignId = $(this).data('campaign-id');
        
        Swal.fire({
            title: 'Annuler la programmation',
            text: 'Annuler la programmation et repasser cette campagne en brouillon?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Non'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create and submit form
                const form = $('<form>')
                    .attr('method', 'POST')
                    .attr('action', `/admin/newsletters/${campaignId}/cancel-schedule`)
                    .append($('<input>').attr('type', 'hidden').attr('name', '_token').val(csrfToken));
                
                $('body').append(form);
                form.submit();
            }
        });
    });
});
