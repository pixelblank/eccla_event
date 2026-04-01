<?php
/**
 * Template: Structure du calendrier et de la modale
 */
if (!defined('ABSPATH')) exit;
?>
<div id="eccla-calendar-wrapper">
    <div id="eccla-calendar"></div>
    
    <!-- Structure de la Modale -->
    <div id="eccla-event-modal">
        <div class="modal-content">
            <span id="close-eccla-modal">&times;</span>
            <span id="modal-event-date"></span>
            <h2 id="modal-event-title"></h2>
            <div id="modal-event-content"></div>
            <div id="modal-event-footer">
                <a id="modal-pdf-link" href="#" target="_blank">Télécharger le PDF</a>
            </div>
        </div>
    </div>
</div>
