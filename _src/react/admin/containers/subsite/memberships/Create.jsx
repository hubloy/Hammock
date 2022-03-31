import React, { PureComponent } from 'react';
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';
import fetchWP from 'utils/fetchWP';

import { toast } from 'react-toastify';

export default class CreateMembership extends PureComponent {

	constructor(props) {
		super(props);
		this.membership_create = React.createRef();
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'memberships-create-toast'});
	}

	handleSubmit(event) {
		event.preventDefault();
		var self = this,
			$form = jQuery(self.membership_create.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();
			

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");

		this.fetchWP.post( 'memberships/save', form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success' );
					setTimeout(function(){
						UIkit.modal(jQuery('#hubloy_membership-add-membership')).hide();
						if ( typeof json.id !== 'undefined' ) {
							window.location.hash = "#/edit/" + json.id;
						}
					}, 1000);
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}

	render() {
		const { hubloy_membership } = this.props;
		var strings = hubloy_membership.strings;
		return (
			<div id="hubloy_membership-add-membership" uk-modal="">
				<div className="uk-modal-dialog">
					<button className="uk-modal-close-default" type="button" uk-close=""></button>
					<div className="uk-modal-header">
						<h2 className="uk-modal-title">{strings.dashboard.add_new.modal.title}</h2>
					</div>
					<div className="uk-modal-body">
						<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.membership_create}>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.labels.name}</legend>
								<div className="uk-form-controls">
									<InputUI name={`membership_name`} placeholder={strings.labels.name} required={true}/>
								</div>
							</div>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.labels.status}</legend>
								<div className="uk-form-controls">
									<div className="hubloy_membership-input">
										<SwitchUI name={`membership_enabled`} class_name={`membership_enabled`} title={hubloy_membership.common.status.disabled} enabled_title={hubloy_membership.common.status.enabled} value={`1`} />
									</div>
								</div>
							</div>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.labels.type}</legend>
								<div className="uk-form-controls">
									<DropDownUI name={`membership_type`} values={hubloy_membership.page_strings.type} class_name={`hubloy_membership-membership-type`}/>
								</div>
							</div>
							<div className="uk-margin hubloy_membership-membership-date">
								<legend className="uk-form-label">{strings.labels.days}</legend>
								<div className="uk-form-controls">
									<InputUI name={`membership_days`} type={`number`}/>
								</div>
							</div>
							<div className="uk-margin hubloy_membership-membership-recurring">
								<legend className="uk-form-label">{strings.labels.recurring_duration}</legend>
								<div className="uk-form-controls">
									<DropDownUI name={`recurring_duration`} values={hubloy_membership.page_strings.duration} />
								</div>
							</div>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.labels.price}</legend>
								<div className="uk-form-controls">
									<div className="uk-inline">
										<span className="uk-form-icon" dangerouslySetInnerHTML={{ __html: hubloy_membership.common.currency_code }} />
										<InputUI name={`membership_price`} placeholder={`0.00`} required={true}/>
									</div>
								</div>
							</div>
							<div className="uk-margin ">
								<button className="uk-button uk-button-primary save-button">{hubloy_membership.common.buttons.save}</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		);
	}
};