<div id="simple-googlebot-visit-admin">
	<figure><img src="{{PLUGIN_LOGO}}" alt="{{PLUGIN_NAME}}" /></figure>
	<div class="admin-content">
		<div class="admin-block" data-type="settings">
			<a href="#" class="dashicons-before">{{TITLE_SETTINGS}}</a>
			<div>
				<div class="sgbv-settings-loading"></div>
				<form id="sgbv-settings-form" method="POST">
					<div class="sgbv-field" data-value="{{FORM_ACTIVE_PAGES_VALUE}}">
						<div class="sgbv-field-info dashicons-before">
							<label>{{FORM_ACTIVE_PAGES_LABEL}}</label>
						</div>
						<select name="active_pages">
							{FOR FORM_BOOLEAN_OPTIONS}
								{IF {{FORM_BOOLEAN_OPTIONS.VALUE}} === {{FORM_ACTIVE_PAGES_VALUE}}}
									<option value="{{FORM_BOOLEAN_OPTIONS.VALUE}}" selected>{{FORM_BOOLEAN_OPTIONS.TEXT}}</option>
								{ELSE}
									<option value="{{FORM_BOOLEAN_OPTIONS.VALUE}}">{{FORM_BOOLEAN_OPTIONS.TEXT}}</option>
								{END IF}
							{END FOR}
						</select>
					</div>
					<div class="sgbv-field" data-value="{{FORM_ACTIVE_ENTRIES_VALUE}}">
						<div class="sgbv-field-info dashicons-before">
							<label>{{FORM_ACTIVE_ENTRIES_LABEL}}</label>
						</div>
						<select name="active_entries">
							{FOR FORM_BOOLEAN_OPTIONS}
								{IF {{FORM_BOOLEAN_OPTIONS.VALUE}} === {{FORM_ACTIVE_ENTRIES_VALUE}}}
									<option value="{{FORM_BOOLEAN_OPTIONS.VALUE}}" selected>{{FORM_BOOLEAN_OPTIONS.TEXT}}</option>
								{ELSE}
									<option value="{{FORM_BOOLEAN_OPTIONS.VALUE}}">{{FORM_BOOLEAN_OPTIONS.TEXT}}</option>
								{END IF}
							{END FOR}
						</select>
					</div>
					<div class="sgbv-field" data-value="{{FORM_ACTIVE_PRODUCTS_VALUE}}">
						<div class="sgbv-field-info dashicons-before">
							<label>{{FORM_ACTIVE_PRODUCTS_LABEL}}</label>
						</div>
						<select name="active_products">
							{FOR FORM_BOOLEAN_OPTIONS}
								{IF {{FORM_BOOLEAN_OPTIONS.VALUE}} === {{FORM_ACTIVE_PRODUCTS_VALUE}}}
									<option value="{{FORM_BOOLEAN_OPTIONS.VALUE}}" selected>{{FORM_BOOLEAN_OPTIONS.TEXT}}</option>
								{ELSE}
									<option value="{{FORM_BOOLEAN_OPTIONS.VALUE}}">{{FORM_BOOLEAN_OPTIONS.TEXT}}</option>
								{END IF}
							{END FOR}
						</select>
					</div>
					<div class="sgbv-field">
						<div>
							<label>{{FORM_ACTIVE_CUSTOM_TYPES_LABEL}}</label>
						</div>
						<div class="custom-post-types-container">
							<ul class="custom-post-types-list">
								<li data-value="{CUSTOM_TYPE_VALUE}">
									<div>{CUSTOM_TYPE_VALUE}</div>
									<a href="#">{{FORM_ACTIVE_CUSTOM_TYPES_REMOVE_BUTTON}}</a>
								</li>
								{FOR FORM_ACTIVE_CUSTOM_TYPES_OBJECT}
									{IF {{FORM_ACTIVE_CUSTOM_TYPES_OBJECT.VALUE}}}
										<li data-value="{{FORM_ACTIVE_CUSTOM_TYPES_OBJECT.VALUE}}">
											<div>{{FORM_ACTIVE_CUSTOM_TYPES_OBJECT.VALUE}}</div>
											<a href="#">{{FORM_ACTIVE_CUSTOM_TYPES_REMOVE_BUTTON}}</a>
										</li>
									{END IF}
								{END FOR}
							</ul>
							<div class="custom-post-types-input">
								<input placeholder="{{FORM_ACTIVE_CUSTOM_TYPES_INPUT_PLACEHOLDER}}" type="text" name="active_custom_types" />
								<a href="#" class="add-custom-post-type dashicons-before"></a>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
		{IF {{LAST_VISITS}}}
			<div class="admin-block admin-block-hidden" data-type="last-visits">
				<a href="#" class="dashicons-before">{{TITLE_LAST_VISITS}}</a>
				<div>
					{FOR LAST_VISITS}
						{IF {{LAST_VISITS.MOBILE}}}
							<div class="admin-block-visit admin-block-visit-mobile">
								<a href="{{LAST_VISITS.URL}}" target="_blank" class="dashicons-before">{{LAST_VISITS.URL}}</a> ({{LAST_VISITS.DATE}})
							</div>
						{ELSE}
							<div class="admin-block-visit">
								<a href="{{LAST_VISITS.URL}}" target="_blank" class="dashicons-before">{{LAST_VISITS.URL}}</a> ({{LAST_VISITS.DATE}})
							</div>
						{END IF}
					{END FOR}
				</div>
			</div>
		{END IF}
		<div class="admin-block admin-block-hidden" data-type="why">
			<a href="#" class="dashicons-before">{{TITLE_WHY}}</a>
			<div>{{BODY_WHY}}</div>
		</div>
		<div class="admin-block admin-block-hidden" data-type="about">
			<a href="#" class="dashicons-before">{{TITLE_ABOUT}}</a>
			<div>{{BODY_ABOUT}}</div>
		</div>
	</div>
	<div class="simple-googlebot-visit-admin-footer">
		<p>{{FOOTER_TEXT}}</p>
	</div>
</div>