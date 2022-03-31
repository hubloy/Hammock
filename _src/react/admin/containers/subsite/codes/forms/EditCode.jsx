import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import { Link } from 'react-router-dom';
import fetchWP from 'utils/fetchWP';
import { InputUI, DropDownUI } from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class EditCode extends Component {

	constructor(props) {
		super(props);

		this.coupon_edit_code = React.createRef();
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
		});
		
		this.state = {
            item: {},
            loading : true,
            error : false,
        };
	}

	notify(message, type) {
		toast[type](message, {toastId: 'edit-code-toast'});
	}

	async componentDidMount() {
		this.fetch_data();
		this.init_scripts()
	}
	
	async componentDidUpdate() {
		this.init_scripts()
	}


	fetch_data = async() => {
		const type = this.props.type;
		const id = this.props.match.params.id;
        this.fetchWP.get( 'codes/get/'+type+'?id=' + id )
            .then( (json) => this.setState({
                item : json,
                loading : false,
                error : false,
            }), (err) => this.setState({ loading : false, error : true, err : err })
        );
	}

	init_scripts() {
		window.hubloy_membership.helper.bind_date_range();
        jQuery('.hubloy_membership-email-tags').tagsInput({width:'98%',  defaultText : this.props.hubloy_membership.strings.select_email});
	}


	handleSubmit( event ) {
		event.preventDefault();
		const type = this.props.type;
		var self = this,
			$form = jQuery(self.coupon_edit_code.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'codes/update/' + type, form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success');
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

	renderCouponForm( strings, page_strings, code ) {
		
		return (
			<React.Fragment>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.code.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`coupon`} placeholder={strings.create.coupons.code.title} value={code.code} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.code.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.amount_type.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`amount_type`} values={page_strings.types} value={code.amount_type} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.amount.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`amount`} placeholder={`0`} required={false} value={code.amount}/>
						<p className="uk-text-meta">{strings.create.coupons.amount.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.status.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`status`} values={page_strings.status} value={code.status} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.expire.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`expire`} class_name={`hubloy_membership-from-date`} placeholder={``} value={typeof code.custom_data.expire !== 'undefined' ? code.custom_data.expire : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.expire.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.restrict.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`restrict`} class_name={`hubloy_membership-email-tags`} placeholder={``} value={typeof code.custom_data.restrict !== 'undefined' ? code.custom_data.restrict : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.coupons.restrict.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.coupons.usage.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`usage`} placeholder={``} required={false} value={typeof code.custom_data.usage !== 'undefined' ? code.custom_data.usage : ''}/>
						<p className="uk-text-meta">{strings.create.coupons.usage.description}</p>
					</div>
				</div>
			</React.Fragment>
		)
	}

	renderInviteForm( strings, page_strings, code ) {
		return (
			<React.Fragment>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.code.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`coupon`} value={code.code} placeholder={strings.create.invites.code.title} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.code.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.status.title}</legend>
					<div className="uk-form-controls">
						<DropDownUI name={`status`} values={page_strings.status} value={code.status} />
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.expire.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`expire`} class_name={`hubloy_membership-from-date`} placeholder={``} value={typeof code.custom_data.expire !== 'undefined' ? code.custom_data.expire : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.expire.description}</p>
					</div>
				</div>
				<div className="uk-margin">
					<legend className="uk-form-label">{strings.create.invites.restrict.title}</legend>
					<div className="uk-form-controls">
						<InputUI name={`restrict`} class_name={`hubloy_membership-email-tags`} placeholder={``} value={typeof code.custom_data.restrict !== 'undefined' ? code.custom_data.restrict : ''} required={false}/>
						<p className="uk-text-meta">{strings.create.invites.restrict.description}</p>
					</div>
				</div>
			</React.Fragment>
		)
	}

	render_form( hubloy_membership ) {
		const type = this.props.type,
			strings = hubloy_membership.strings,
			page_strings = hubloy_membership.page_strings,
			code = this.state.item;
		return (
			<Dashboard hubloy_membership={hubloy_membership} title={type === 'coupons' ? strings.edit.coupon : strings.edit.invite}>
				<div className={"uk-background-default uk-padding-small uk-margin-small-top hubloy_membership-settings-" + type}>
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.coupon_edit_code}>
						<InputUI name={`type`} type={`hidden`} value={this.props.type}/>
						<InputUI name={`id`} type={`hidden`} value={code.id}/>
						{type === 'coupons' ? (
							this.renderCouponForm( strings, page_strings, code )
						) : (
							this.renderInviteForm( strings, page_strings, code )
						)}
						<div className="uk-margin uk-button-group">
							<button className="uk-button uk-button-primary save-button">{hubloy_membership.common.buttons.update}</button>
							<Link to={'/'} className="uk-button uk-button-secondary uk-margin-small-left">{hubloy_membership.common.buttons.back}</Link>
						</div>
					</form>
				</div>
			</Dashboard>
		)
	}

	render() {
		const hubloy_membership = this.props.hubloy_membership;
		if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				
				return (
					<h3 className="uk-text-center uk-text-danger">{hubloy_membership.error}</h3>
				)
			} else {
				return this.render_form( hubloy_membership );
			}
		}
	}

}
EditCode.propTypes = {
	hubloy_membership: PropTypes.object
};