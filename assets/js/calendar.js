/**
 * ECCLA Agenda - Calendar and Modal Logic (Verdant Shore Edition)
 */
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('eccla-calendar');
    if (!calendarEl) return;

    var modal = document.getElementById('eccla-event-modal');
    var closeBtn = document.getElementById('close-eccla-modal');
    var body = document.body;

    // Helper pour calculer le contraste (noir ou blanc) selon la couleur de fond
    function getContrastYIQ(hexcolor){
        if (!hexcolor) return '#181d1a';
        hexcolor = hexcolor.replace("#", "");
        var r = parseInt(hexcolor.substr(0,2),16);
        var g = parseInt(hexcolor.substr(2,2),16);
        var b = parseInt(hexcolor.substr(4,2),16);
        var yiq = ((r*299)+(g*587)+(b*114))/1000;
        return (yiq >= 128) ? '#181d1a' : '#ffffff';
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'title',
            center: '',
            right: 'prev,next'
        },
        buttonText: { today: "Aujourd'hui" },
        events: eccla_agenda_data.events,
        
        // Appliquer la couleur et le contraste lors du rendu
        eventDidMount: function(info) {
            if (info.event.backgroundColor) {
                var bgColor = info.event.backgroundColor;
                info.el.style.setProperty('background-color', bgColor, 'important');
                
                var titleEl = info.el.querySelector('.fc-event-title');
                if (titleEl) {
                    var textColor = getContrastYIQ(bgColor);
                    titleEl.style.setProperty('color', textColor, 'important');
                }
            }
        },

        // Personnalisation des cellules de jour
        dayCellDidMount: function(info) {
            var d = info.date;
            var year = d.getFullYear();
            var month = ('0' + (d.getMonth() + 1)).slice(-2);
            var day = ('0' + d.getDate()).slice(-2);
            var dateStr = year + '-' + month + '-' + day;
            var currentTime = new Date(dateStr).getTime();

            var hasEvent = eccla_agenda_data.events.some(function(evt) {
                var eventStart = new Date(evt.start.split('T')[0]).getTime();
                // Si pas de date de fin explicitée dans le JSON, on prend la date de début
                var eventEnd = evt.end ? new Date(evt.end.split('T')[0]).getTime() : eventStart;
                
                // FullCalendar utilise des dates de fin exclusives (+1 jour), 
                // donc on vérifie si notre jour est >= début ET < fin
                return currentTime >= eventStart && currentTime < eventEnd;
            });
            
            if (hasEvent) {
                info.el.classList.add('has-event');
            }
        },

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

            modal.style.display = 'flex';
            body.style.overflow = 'hidden';
        }
    });

    calendar.render();

    function closeEcclaModal() {
        modal.style.display = 'none';
        body.style.overflow = '';
    }

    if (closeBtn) closeBtn.onclick = closeEcclaModal;
    window.onclick = function(event) { if (event.target == modal) { closeEcclaModal(); } }
});
