import React, { PureComponent } from 'react';
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';
import fetchWP from 'utils/fetchWP';

import { toast } from 'react-toastify';

export default class WizardCreateMembership extends PureComponent {

    constructor(props) {
		super(props);
		this.membership_create = React.createRef();
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });

        this.handleSubmit = this.handleSubmit.bind(this);
	}

    notify(type, message) {
		toast[type](message, {toastId: 'wizard-membership-toast'});
	}

	handleSubmit(event) {
		event.preventDefault();
		var self = this,
			$form = jQuery(self.membership_create.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize(),
            hubloy_membership = this.props.hubloy_membership;
			

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");

		this.fetchWP.post( 'wizard/membership', form, true )
			.then( (json) => {
				if ( json.status ) {
                    self.notify( 'success', json.message );
                    setTimeout(function(){
                        window.location.reload();
                    }, 2000);
				} else {
					self.notify( 'warning', json.message );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( 'error', hubloy_membership.error );
			}
		);
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership,
            strings = hubloy_membership.strings.dashboard.wizard.membership;
		return (
            <form className="uk-form-horizontal uk-margin-small" onSubmit={this.handleSubmit} ref={this.membership_create}>
                <h2 className="uk-heading-divider">{strings.title}</h2>
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
                        <DropDownUI name={`membership_type`} values={strings.type} class_name={`hubloy_membership-membership-type`}/>
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
                        <DropDownUI name={`recurring_duration`} values={strings.duration} />
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
					
		);
	}
}