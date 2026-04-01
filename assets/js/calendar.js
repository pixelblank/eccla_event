/**
 * ECCLA Agenda - Calendar and Modal Logic
 */
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('eccla-calendar');
    if (!calendarEl) return;

    var modal = document.getElementById('eccla-event-modal');
    var closeBtn = document.getElementById('close-eccla-modal');
    var body = document.body;

    // Les événements sont passés via wp_localize_script (variable eccla_agenda_data)
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        buttonText: { today: "Aujourd'hui" },
        events: eccla_agenda_data.events,
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            
            // Remplir la modale
            document.getElementById('modal-event-title').innerText = info.event.title;
            
            var timeHtml = info.event.extendedProps.formatted_date;
            if (info.event.extendedProps.display_time) {
                timeHtml += ' | ' + info.event.extendedProps.display_time;
            }
            document.getElementById('modal-event-date').innerText = timeHtml;
            document.getElementById('modal-event-content').innerHTML = info.event.extendedProps.description;
            
            var pdfLink = document.getElementById('modal-pdf-link');
            if (info.event.extendedProps.pdf) {
                pdfLink.href = info.event.extendedProps.pdf;
                pdfLink.style.display = 'inline-block';
            } else {
                pdfLink.style.display = 'none';
            }

            // Afficher la modale et bloquer le scroll
            modal.style.display = 'flex';
            body.style.overflow = 'hidden';
        }
    });

    calendar.render();

    // Fonction de fermeture
    function closeEcclaModal() {
        modal.style.display = 'none';
        body.style.overflow = '';
    }

    if (closeBtn) closeBtn.onclick = closeEcclaModal;
    
    window.onclick = function(event) {
        if (event.target == modal) {
            closeEcclaModal();
        }
    }
});
