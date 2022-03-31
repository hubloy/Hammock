import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import { InputUI, DropDownUI } from 'ui/admin/form';
import fetchWP from 'utils/fetchWP';

import { toast } from 'react-toastify';

export default class EditTransaction extends Component {

	constructor(props) {
		super(props);
		this.update_transaction = React.createRef();
		this.state = {
			id : this.props.match.params.id,
			invoice : {},
			status : [],
			loading : true,
			error : false
		};
		
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(message, type) {
		toast[type](message, {toastId: 'transactions-edit-toast'});
	}

	async componentDidMount() {
		Promise.all([ this.load_invoice(), this.load_status()]);
	}

	load_invoice = async () => {
		const id = this.state.id;
		this.fetchWP.get( 'transactions/get?id=' + id )
			.then( (json) => this.setState({
				invoice : json,
				loading : false,
				error : false
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	load_status = async () => {
		this.fetchWP.get( 'transactions/list/status' )
			.then( (json) => this.setState({
				status : json
			}), (err) => this.setState({ loading : false, error : true })
		);
	}
	
	handleSubmit(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.update_transaction.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'transactions/update', form, true )
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

	componentDidUpdate() {
		window.hubloy_membership.helper.bind_date_range();
	}

	render_form( hubloy_membership, invoice ) {
		var strings = hubloy_membership.strings;
		return (
			<Dashboard hubloy_membership={hubloy_membership} title={strings.update.title}>
				<div className="hubloy_membership-transaction uk-background-default uk-padding-small">
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.update_transaction}>
						<InputUI name={`id`} type={`hidden`} value={invoice.id}/>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.update.form.member}</label>
							<div className="uk-form-controls">
								{invoice.member_user.user_info.name}
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.update.form.membership}</label>
							<div className="uk-form-controls">
								{invoice.plan.membership.name}
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.update.form.status}</label>
							<div className="uk-form-controls">
								<DropDownUI name={`status`} values={this.state.status} value={invoice.status} required={true}/>
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.update.form.amount}</label>
							<div className="uk-form-controls">
								<div className="uk-inline">
									<span className="uk-form-icon" dangerouslySetInnerHTML={{ __html: this.props.hubloy_membership.common.currency_code }} />
									<InputUI name={`amount`} placeholder={`0.00`} required={true} value={invoice.amount}/>
								</div>
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.update.form.gateway}</label>
							<div className="uk-form-controls">
								{invoice.gateway_name}
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.update.form.date}</label>
							<div className="uk-form-controls">
								<InputUI name={`due_date`} class_name={`hubloy_membership-from-date`} value={invoice.due}/>
							</div>
						</div>
						<div className="uk-margin ">
							<button className="uk-button uk-button-primary save-button">{hubloy_membership.common.buttons.update}</button>
						</div>
					</form>
				</div>
			</Dashboard>
		)
	}

	render() {
		const hubloy_membership = this.props.hubloy_membership,
			invoice = this.state.invoice;
		if ( this.state.loading) {
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
			} else if ( invoice.id > 0 ) {
				return this.render_form( hubloy_membership, invoice );
			}
		}
		
	}
}

EditTransaction.propTypes = {
	hubloy_membership: PropTypes.object
};