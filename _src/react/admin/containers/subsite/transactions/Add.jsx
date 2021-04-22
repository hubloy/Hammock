import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

import Dashboard from '../layout/Dashboard'
import { SwitchUI, InputUI, DropDownUI } from '../../../ui/admin/form';
import fetchWP from '../../../../utils/fetchWP';
import {Members} from '../common/Members'

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
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
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
			form = $form.serialize(),
			helper = window.hammock.helper;
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'transactions/save', form, true )
			.then( (json) => {
				if ( json.status ) {
					helper.notify( json.message, 'success', function() {
						if ( typeof json.id !== 'undefined' ) {
							window.location.hash = "#/transaction/" + json.id;
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

	componentDidUpdate() {
		window.hammock.helper.bind_date_range();
	}

	render_form( hammock ) {
		var strings = hammock.strings;
		return (
			<Dashboard hammock={hammock}>
				<div uk-grid="">
					<div className="uk-width-1-4 uk-height-medium">
						<h2 className="uk-heading-divider">{strings.create.title}</h2>
						<Link className="uk-border-rounded uk-margin-bottom uk-background-default uk-button uk-button-default uk-button-small" to="/">{strings.back}</Link>
					</div>
					<div className="hammock-transaction uk-width-expand uk-margin-left uk-card uk-card-body uk-background-default">
						<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.add_transaction}>
							<div className="uk-margin">
								<label className="uk-form-label">{strings.create.form.member}</label>
								<div className="uk-form-controls">
									<Members hammock={hammock}/>
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
									<InputUI name={`due_date`} class_name={`hammock-from-date`} />
								</div>
							</div>
							<div className="uk-margin ">
								<button className="uk-button uk-button-primary save-button">{hammock.common.buttons.save}</button>
							</div>
						</form>
					</div>
				</div>
			</Dashboard>
		)
	}

	render() {
		const hammock = this.props.hammock;
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
			} else {
				return this.render_form( hammock );
			}
		}
		
	}
}

AddTransaction.propTypes = {
	hammock: PropTypes.object
};