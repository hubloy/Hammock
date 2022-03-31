import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import { InputUI, DropDownUI } from 'ui/admin/form';
import fetchWP from 'utils/fetchWP';
import {Members} from '../common/Members'

import { toast } from 'react-toastify';

export default class AddTransaction extends Component {

	constructor(props) {
		super(props);
		this.add_transaction = React.createRef();
		this.state = {
			memberships : [],
			status : [],
			gateways : [],
			loading : true,
			error : false
		};
		
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(message, type) {
		toast[type](message, {toastId: 'transactions-add-toast'});
	}

	async componentDidMount() {
		Promise.all([ this.load_memberships(), this.load_status() , this.load_gateways()]);
	}

	load_memberships = async () => {
		this.fetchWP.get( 'memberships/list_simple' )
			.then( (json) => this.setState({
				memberships : json,
				loading : false,
				error : false
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	load_status = async () => {
		this.fetchWP.get( 'transactions/list/status' )
			.then( (json) => this.setState({
				status : json,
				loading : false,
				error : false
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	load_gateways = async () => {
		this.fetchWP.get( 'gateways/list_simple' )
			.then( (json) => this.setState({
				gateways : json,
				loading : false,
				error : false
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	handleSubmit(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.add_transaction.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'transactions/save', form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success' );
					setTimeout(function(){
						if ( typeof json.id !== 'undefined' ) {
							window.location.hash = "#/transaction/" + json.id;
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
				self.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

	componentDidUpdate() {
		window.hubloy_membership.helper.bind_date_range();
	}

	render_form( hubloy_membership ) {
		var strings = hubloy_membership.strings;
		return (
			<Dashboard hubloy_membership={hubloy_membership} title={strings.create.title}>
				<div className="hubloy_membership-transaction uk-background-default uk-padding-small">
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.add_transaction}>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.create.form.member}</label>
							<div className="uk-form-controls">
								<Members hubloy_membership={hubloy_membership}/>
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.create.form.membership}</label>
							<div className="uk-form-controls">
								<DropDownUI name={`membership`} values={this.state.memberships} required={true}/>
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.create.form.status}</label>
							<div className="uk-form-controls">
								<DropDownUI name={`status`} values={this.state.status} required={true}/>
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.create.form.gateway}</label>
							<div className="uk-form-controls">
								<DropDownUI name={`gateway`} values={this.state.gateways} required={true}/>
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label">{strings.create.form.date}</label>
							<div className="uk-form-controls">
								<InputUI name={`due_date`} class_name={`hubloy_membership-from-date`} />
							</div>
						</div>
						<div className="uk-margin ">
							<button className="uk-button uk-button-primary save-button">{hubloy_membership.common.buttons.save}</button>
						</div>
					</form>
				</div>
			</Dashboard>
		)
	}

	render() {
		const hubloy_membership = this.props.hubloy_membership;
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
			} else {
				return this.render_form( hubloy_membership );
			}
		}
		
	}
}

AddTransaction.propTypes = {
	hubloy_membership: PropTypes.object
};