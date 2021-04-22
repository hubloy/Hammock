import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import Dashboard from '../../layout/Dashboard'
import { Link } from 'react-router-dom';
import fetchWP from '../../../../../utils/fetchWP';
import { SwitchUI, InputUI, DropDownUI } from '../../../../ui/admin/form';

export default class CreateCode extends Component {

	constructor(props) {
		super(props);

		this.coupon_create_code = React.createRef();

		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}


	async componentDidMount() {
		window.hammock.helper.bind_date_range();
        jQuery('.hammock-email-tags').tagsInput({width:'98%',  defaultText : this.props.hammock.strings.select_email});
    }

	handleSubmit( event ) {
		event.preventDefault();
		const type = this.props.type;
		var self = this,
			$form = jQuery(self.coupon_create_code.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize(),
			helper = window.hammock.helper;
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'codes/save/' + type, form, true )
			.then( (json) => {
				if ( json.status ) {
					helper.notify( json.message, 'success', function() {
						if ( typeof json.id !== 'undefined' ) {
							window.location.hash = "#/edit/" + json.id;
						}
					} );
				} else {
					helper.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				helper.notify( self.props.hammock.error, 'error' );
			}
		);
	}

	renderCouponForm( strings, page_strings ) {
		return (
			<React.Fragment>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.code.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`coupon`} placeholder={strings.create.coupons.code.title} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.code.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.amount_type.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`amount_type`} values={page_strings.types} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.amount.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`amount`} placeholder={`0`} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.amount.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.status.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`status`} values={page_strings.status} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.expire.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`expire`} class_name={`hammock-from-date`} placeholder={``} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.expire.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.restrict.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`restrict`} class_name={`hammock-email-tags`} placeholder={``} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.restrict.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.usage.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`usage`} placeholder={``} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.usage.description}</p>
					</div>
				</div>
			</React.Fragment>
		)
	}

	renderInviteForm( strings, page_strings ) {
		return (
			<React.Fragment>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.code.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`coupon`} placeholder={strings.create.invites.code.title} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.code.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.status.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`status`} values={page_strings.status} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.expire.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`expire`} class_name={`hammock-from-date`} placeholder={``} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.expire.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.restrict.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`restrict`} class_name={`hammock-email-tags`} placeholder={``} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.restrict.description}</p>
					</div>
				</div>
			</React.Fragment>
		)
	}

	render() {
		const type = this.props.type,
			hammock = this.props.hammock,
			strings = hammock.strings,
			page_strings = hammock.page_strings;
		return (
			<Dashboard hammock={hammock}>
				<h2 className="uk-text-center uk-heading-divider">
					{type === 'coupons' ? (
						strings.add.coupon
					) : ( 
						strings.add.invite
					)}
				</h2>
				<Link to={'/'} className="uk-button uk-button-primary uk-button-small">{hammock.common.buttons.back}</Link>
				<div className={"uk-background-default uk-padding-small uk-margin-small-top hammock-settings-" + type}>
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.coupon_create_code}>
						{type === 'coupons' ? (
							this.renderCouponForm( strings, page_strings )
						) : (
							this.renderInviteForm( strings, page_strings )
						)}
						<div className="uk-margin">
							<button className="uk-button uk-button-primary save-button">{hammock.common.buttons.save}</button>
						</div>
					</form>
				</div>
			</Dashboard>
		)
	}

}
CreateCode.propTypes = {
	hammock: PropTypes.object
};