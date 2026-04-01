<?php
/**
 * Template: Meta Box Administration
 */
if (!defined('ABSPATH')) exit;
?>
<p>
    <label><strong>Date et Heure :</strong></label><br>
    <div style="display: flex; gap: 10px; margin-top: 5px;">
        <div style="flex: 1;">
            <small>Date début</small><br>
            <input type="date" name="event_date" value="<?php echo esc_attr($date); ?>" style="width:100%;">
        </div>
        <div style="flex: 1;">
            <small>Date fin (optionnel)</small><br>
            <input type="date" name="event_end_date" value="<?php echo esc_attr(get_post_meta($post->ID, '_event_end_date', true)); ?>" style="width:100%;">
        </div>
    </div>
    <div style="display: flex; gap: 10px; margin-top: 10px;">
        <div style="flex: 1;">
            <small>Heure début (optionnel)</small><br>
            <input type="time" name="event_start_time" value="<?php echo esc_attr(get_post_meta($post->ID, '_event_start_time', true)); ?>" style="width:100%;">
        </div>
        <div style="flex: 1;">
            <small>Heure fin (optionnel)</small><br>
            <input type="time" name="event_end_time" value="<?php echo esc_attr(get_post_meta($post->ID, '_event_end_time', true)); ?>" style="width:100%;">
        </div>
    </div>
</p>
<p>
    <label><strong>Document associé :</strong></label><br>
    <input type="text" name="event_pdf" id="event_pdf" value="<?php echo esc_attr($pdf); ?>" style="width:80%;">
    <button type="button" class="button" id="upload_pdf_btn">Choisir PDF</button>
</p>
<script>
    jQuery('#upload_pdf_btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ title: 'Choisir un PDF', multiple: false }).open().on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            jQuery('#event_pdf').val(image_url);
        });
    });
</script>
