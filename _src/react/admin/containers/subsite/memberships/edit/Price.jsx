import React, { PureComponent } from 'react';

import fetchWP from 'utils/fetchWP'
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class Price extends PureComponent {

	constructor(props) {
		super(props);
		this.membership_price = React.createRef();
		this.state = {
            membership: this.props.membership
        };
		
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });

		this.handleSubmit = this.handleSubmit.bind(this);
	}

	notify(type, message) {
		toast[type](message, {toastId: 'memberships-edit-price-toast'});
	}

	handleSubmit(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.membership_price.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();
			

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");

		this.fetchWP.post( 'memberships/update/price', form, true )
			.then( (json) => {
				if ( json.status ) {
					this.setState({
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
				self.notify( this.props.hammock.error, 'error' );
			}
		);
	}

	render() {
		const membership = this.state.membership;
		var hammock = this.props.hammock;
		var strings = hammock.strings;
		return (
			<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit} ref={this.membership_price}>
				<InputUI name={`id`} type={`hidden`} value={membership.id}/>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.labels.price}</legend>
					<div className="uk-form-controls">
						<div className="uk-inline">
							<span className="uk-form-icon" dangerouslySetInnerHTML={{ __html: this.props.hammock.common.currency_code }} />
							<InputUI name={`membership_price`} placeholder={`0.00`} required={true} value={membership.price}/>
						</div>
					</div>
				</div>
				<div className="uk-margin hammock-membership-recurring" style={{display: ( membership.type === 'recurring' ? 'block' : 'none' )}}>
					<legend className="uk-form-label">{strings.labels.signup_price}</legend>
					<div className="uk-form-controls">
						<div className="uk-inline">
							<span className="uk-form-icon" dangerouslySetInnerHTML={{ __html: this.props.hammock.common.currency_code }} />
							<InputUI name={`signup_price`} placeholder={`0.00`} required={true} value={membership.signup_price}/>
						</div>
					</div>
				</div>
				
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.labels.trial}</legend>
					<div className="uk-form-controls">
						<div className="hammock-input">
							<SwitchUI name={`trial_enabled`} checked={membership.trial_enabled} class_name={`hammock-trial_enabled`} title={this.props.hammock.common.status.disabled} enabled_title={this.props.hammock.common.status.enabled} value={`1`} />
						</div>
					</div>
				</div>
				<div className="uk-margin hammock-membership-trial" style={{display: ( membership.trial_enabled ? 'block' : 'none' )}}>
					<legend className="uk-form-label">{strings.labels.trial_price}</legend>
					<div className="uk-form-controls">
						<div className="uk-inline">
							<span className="uk-form-icon" dangerouslySetInnerHTML={{ __html: hammock.common.currency_code }} />
							<InputUI name={`trial_price`} value={membership.trial_price} class_name={`membership_trial_price`} placeholder={`0.00`}/>
						</div>
					</div>
				</div>
				<div className="uk-margin hammock-membership-trial" style={{display: ( membership.trial_enabled ? 'block' : 'none' )}}>
					<legend className="uk-form-label">{strings.labels.trial_duration}</legend>
					<div className="uk-form-controls">
						<div className="uk-grid-small" uk-grid="">
							<div className="uk-width-1-2@s">
								<InputUI type={`number`} name={`trial_period`} value={membership.trial_period} class_name={`membership_trial_period`} placeholder={`1`}/>
							</div>
							<div className="uk-width-1-2@s">
								<DropDownUI name={`trial_duration`} values={hammock.page_strings.trial_period} value={membership.trial_duration}/>
							</div>
						</div>
					</div>
				</div>
				<div className="uk-margin ">
					<button className="uk-button uk-button-primary update-button">{hammock.common.buttons.update}</button>
				</div>
			</form>
		)
	}
};