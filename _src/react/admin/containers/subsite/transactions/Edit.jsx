import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

import Dashboard from '../layout/Dashboard'
import { SwitchUI, InputUI, DropDownUI } from '../../../ui/admin/form';
import fetchWP from '../../../../utils/fetchWP';
import {Members} from '../common/Members'

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
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
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
			form = $form.serialize(),
			helper = window.hammock.helper;
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'transactions/update', form, true )
			.then( (json) => {
				if ( json.status ) {
					helper.notify( json.message, 'success');
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

	componentDidUpdate() {
		window.hammock.helper.bind_date_range();
	}

	render_form( hammock, invoice ) {
		var strings = hammock.strings;
		return (
			<Dashboard hammock={hammock}>
				<div uk-grid="">
					<div className="uk-width-1-4 uk-height-medium">
						<h2 className="uk-heading-divider">{strings.update.title}</h2>
						<Link className="uk-border-rounded uk-margin-bottom uk-background-default uk-button uk-button-default uk-button-small" to="/">{strings.back}</Link>
					</div>
					<div className="hammock-transaction uk-width-expand uk-margin-left uk-card uk-card-body uk-background-default">
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
										<span className="uk-form-icon" dangerouslySetInnerHTML={{ __html: this.props.hammock.common.currency_code }} />
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
									<InputUI name={`due_date`} class_name={`hammock-from-date`} value={invoice.due}/>
								</div>
							</div>
							<div className="uk-margin ">
								<button className="uk-button uk-button-primary save-button">{hammock.common.buttons.update}</button>
							</div>
						</form>
					</div>
				</div>
			</Dashboard>
		)
	}

	render() {
		const hammock = this.props.hammock,
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
					<h3 className="uk-text-center uk-text-danger">{hammock.error}</h3>
				)
			} else if ( invoice.id > 0 ) {
				return this.render_form( hammock, invoice );
			}
		}
		
	}
}

EditTransaction.propTypes = {
	hammock: PropTypes.object
};