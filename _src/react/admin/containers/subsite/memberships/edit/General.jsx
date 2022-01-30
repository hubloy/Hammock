import React, { PureComponent } from 'react';
import { Multiselect } from 'multiselect-react-dropdown';

import fetchWP from 'utils/fetchWP'
import { SwitchUI, InputUI, DropDownUI, TextAreaUI } from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class General extends PureComponent {

	constructor(props) {
		super(props);
		this.membership_general = React.createRef();
		this.state = {
			membership: this.props.membership,
			invite_active: false,
			invites:[]
        };
		
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });

		this.handleSubmit = this.handleSubmit.bind(this);
	}


	notify(message,type) {
		toast[type](message, {toastId: 'memberships-edit-general-toast'});
	}

	async componentDidMount() {
		Promise.all([this.checkInviteStatus(), this.loadInviteCodes()]);
		this.initWPEditor();
	}

	initWPEditor() {
		if ( typeof window.wp.editor !== 'undefined' ) {
			window.wp.editor.initialize( 'membership_details_field', {
				tinymce: {
					wpautop: true,
					plugins : 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
					toolbar1: 'bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen | wp_adv',
					toolbar2: 'formatselect alignjustify forecolor | pastetext removeformat charmap | outdent indent | undo redo | wp_help'
				},
				quicktags: true,
				mediaButtons: false,
			  } 
			);
		}
	}

	getWPEditorContent() {
		var editor_id = 'membership_details_field',
			mce_editor = window.tinymce.get(editor_id),
			val = '';
		if ( mce_editor ) {
			val = window.wp.editor.getContent(editor_id); // Visual tab is active
		} else {
			val = jQuery('#'+editor_id).val(); // HTML tab is active
		}
		return val;
	}

	/**
	 * Check if the invite addon is enabled
	 */
	checkInviteStatus = async() => {
		this.fetchWP.get( 'addons/settings?name=invitation' )
			.then( (json) => this.setState({
				invite_active : json.enabled
			}), (err) => console.log(err)
		);
	}


	/**
	 * Load codes
	 */
	loadInviteCodes = async() => {
		this.fetchWP.get( 'codes/dropdown/invitation' )
			.then( (json) => this.setState({
				invites : json,
			}), (err) => self.notify( this.props.hammock.error, 'error' )
		);
	}

	handleSubmit(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.membership_general.current),
			$button = $form.find('button.update-button'),
			$details = $form.find('input.membership_details'),
			$btn_txt = $button.text(),
			$detail_content = self.getWPEditorContent();
			
		$details.val($detail_content);

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");

		self.fetchWP.post( 'memberships/update/general', $form.serialize(), true )
			.then( (json) => {
				if ( json.status ) {
					self.setState({
						membership : json.membership
					});
					self.notify( json.message, 'success' );
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( self.props.hammock.error, 'error' );
			}
		);
	}

	/**
	 * Show/hide invites
	 * 
	 * @param {bool} checked 
	 */
	toggle_invites( checked ) {
		var access = document.getElementsByClassName('hammock-membership-invite-list');
		access = access[0];
		if ( checked ) {
			access.style.display = "block";
		} else {
			access.style.display = "none";
		}
	}

	toggle_setup_price( target, value ) {
		var access = document.getElementsByClassName('hammock-membership-recurring');
		access = access[0];
		if ( value === 'recurring' ) {
			access.style.display = "block";
		} else {
			access.style.display = "none";
		}
	}
	
	on_invite_select(selectedList, selectedItem) {
		this.fill_invite_list(selectedList);
	}

	on_invite_remove(selectedList, selectedItem) {
		this.fill_invite_list(selectedList);
	}

	fill_invite_list( selectedList ) {
		var end_list = [];
		selectedList.forEach(function(item) {
			end_list.push(item.id);
		});
		jQuery('.invite_only_list').val(end_list.join(','));
	}

	render() {
		const membership = this.state.membership;
		var hammock = this.props.hammock;
		var strings = hammock.strings;
		const invite_only = typeof membership.meta.invite_only === 'undefined' ? 0 : membership.meta.invite_only.meta_value,
			invite_only_list = typeof membership.meta.invite_list === 'undefined' ? [] : membership.meta.invite_list.meta_value;
		return (
			<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit} ref={this.membership_general}>
				<InputUI name={`id`} type={`hidden`} value={membership.id}/>
				<InputUI name={`invite_only_list`} type={`hidden`} value={invite_only_list.join(',')}/>
				<InputUI name={`membership_details`} type={`hidden`} value={membership.details}/>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.labels.name}</legend>
					<div className="uk-form-controls">
						<InputUI name={`membership_name`} value={membership.name} placeholder={strings.labels.name} required={true}/>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.labels.details}</legend>
					<div className="uk-form-controls">
						<TextAreaUI id={`membership_details_field`} name={`membership_details_field`} value={membership.details} placeholder={strings.labels.details} required={false}/>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.labels.status}</legend>
					<div className="uk-form-controls">
						<div className="hammock-input">
							<SwitchUI name={`membership_enabled`} checked={membership.enabled} class_name={`membership_enabled`} title={this.props.hammock.common.status.disabled} enabled_title={this.props.hammock.common.status.enabled} value={`1`} />
						</div>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.labels.type}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`membership_type`} values={hammock.page_strings.type} value={membership.type} class_name={`hammock-membership-type`} action={this.toggle_setup_price.bind(this)}/>
					</div>
				</div>
				<div className="uk-margin hammock-membership-date" style={{display: ( membership.type === 'date-range' ? 'block' : 'none' )}}>
					<legend className="uk-form-label">{strings.labels.days}</legend>
					<div className="uk-form-controls">
						<InputUI name={`membership_days`} type={`number`} value={typeof membership.meta.membership_days === 'undefined' ? '' : membership.meta.membership_days.meta_value}/>
					</div>
				</div>
				<div className="uk-margin hammock-membership-recurring" style={{display: ( membership.type === 'recurring' ? 'block' : 'none' )}}>
					<legend className="uk-form-label">{strings.labels.recurring_duration}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`recurring_duration`} values={hammock.page_strings.duration} value={membership.duration}/>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.labels.limit_access}</legend>
					<div className="uk-form-controls">
						<div className="hammock-input">
							<SwitchUI name={`limit_spaces`} checked={membership.limit_spaces} class_name={`hammock-limit_spaces`} title={this.props.hammock.common.status.disabled} enabled_title={this.props.hammock.common.status.enabled} value={`1`} />
						</div>
					</div>
				</div>
				<div className="uk-margin hammock-membership-limited" style={{display: ( membership.limit_spaces ? 'block' : 'none' )}}>
					<legend className="uk-form-label">{strings.labels.total_available}</legend>
					<div className="uk-form-controls">
						<InputUI type={`number`} name={`total_available`} placeholder={`0`} required={true} value={membership.total_available}/>
						<p className="uk-meta">
							{strings.labels.total_available_desc}
						</p>
					</div>
				</div>
				{this.state.invite_active &&
					<React.Fragment>
						<div className="uk-margin">
							<legend className="uk-form-label">{strings.labels.invite_only}</legend>
							<div className="uk-form-controls">
								<div className="hammock-input">
									<SwitchUI name={`invite_only`} checked={invite_only} class_name={`membership_invite_only`} title={this.props.hammock.common.status.disabled} enabled_title={this.props.hammock.common.status.enabled} value={`1`} action={this.toggle_invites.bind(this)}/>
								</div>
							</div>
						</div>
						<div className="uk-margin hammock-membership-invite-list" style={{display: ( invite_only ? 'block' : 'none' )}}>
							<legend className="uk-form-label">{strings.labels.invite_list}</legend>
							<div className="uk-form-controls">
								<Multiselect options={this.state.invites} placeholder={strings.edit.invites.select} emptyRecordMsg={strings.edit.invites.empty} id={`invite_list`} selectedValues={membership.invite_list} displayValue="name" onSelect={this.on_invite_select.bind(this)} onRemove={this.on_invite_remove.bind(this)}/>
							</div>
						</div>
					</React.Fragment>
				}
				<div className="uk-margin ">
					<button className="uk-button uk-button-primary update-button">{hammock.common.buttons.update}</button>
				</div>
			</form>
		)
	}
}