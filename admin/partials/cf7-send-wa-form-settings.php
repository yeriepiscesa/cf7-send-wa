<div class="contact-form-editor-box-mail" id="<?php echo $id; ?>">
	<h2><?php echo esc_html( $args['title'] ); ?></h2>
	
	<?php
		if ( ! empty( $args['use'] ) ) :
	?>
	<label for="<?php echo $id; ?>-active"><input type="checkbox" id="<?php echo $id; ?>-active" name="<?php echo $id; ?>[active]" class="toggle-form-table" value="1"<?php echo ( $whatsapp['active'] ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( $args['use'] ); ?></label>
	<p class="description"><?php echo esc_html( __( "Only work for WhatsApp API", 'cf7sendwa' ) ); ?></p>
	<?php
		endif;
	?>
	<fieldset>
		<legend>
		In the following fields, you can use these mail-tags:
		<br><?php $post->suggest_mail_tags( $args['name'] ); ?>
		</legend>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="<?php echo $id; ?>-recipient"><?php echo esc_html( __( 'To Number', 'cf7sendwa' ) ); ?></label>
					</th>
					<td>			
						<input type="text" id="<?php echo $id; ?>-recipient" 
						       name="<?php echo $id; ?>[recipient]" 
						       class="large-text code" size="70" placeholder="<?php echo $whatsapp_number ?>"
						       value="<?php echo esc_attr( $whatsapp['recipient'] ); ?>" 
						       data-config-field="<?php echo sprintf( '%s.recipient', esc_attr( $args['name'] ) ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo $id; ?>-body"><?php echo esc_html( __( 'Message body', 'cf7sendwa' ) ); ?></label>
					</th>
					<td>
						<textarea id="<?php echo $id; ?>-body" 
							name="<?php echo $id; ?>[body]" 
							cols="100" rows="18" class="large-text code" 
							data-config-field="<?php echo sprintf( '%s.body', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $whatsapp['body'] ); ?></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="<?php echo $id; ?>-attachments"><?php echo esc_html( __( 'File attachments', 'cf7sendwa' ) ); ?></label>
					</th>
					<td>
						<textarea id="<?php echo $id; ?>-attachments" 
							name="<?php echo $id; ?>[attachments]" 
							cols="100" rows="4" class="large-text code" 
							data-config-field="<?php echo sprintf( '%s.attachments', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $whatsapp['attachments'] ); ?></textarea>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
</div>