import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';
import { InputUI } from 'ui/admin/form';
import Dashboard from 'layout/Dashboard';

import { Link } from 'react-router-dom';

import { toast } from 'react-toastify';

export default class Manage extends Component {

	constructor(props) {
		super(props);

		this.comm_setting = React.createRef();
		this.handleSubmit = this.handleSubmit.bind(this);
		this.state = {
			item : {},
			id : this.props.match.params.id
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'comms-manage-toast'});
	}

	componentDidMount() {
		const id = this.state.id;
		this.fetchWP.get( 'emails/get?id=' + id )
			.then( (json) => this.setState({
				item : json
			}), (err) => console.log( 'error', err )
		);
	}

	handleSubmit(event) {
		event.preventDefault();
		var self = this,
			$form = jQuery(self.comm_setting.current),
			$button = $form.find('button.update-button'),
			$btn_txt = $button.text(),
			form = $form.serialize();

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'emails/update', form, true )
			.then( (json) => {
				if ( json.status ) {
					self.notify( json.message, 'success' );
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
		var hubloy_membership = this.props.hubloy_membership;
		return(
			<Dashboard hubloy_membership={hubloy_membership}>
				{this.state && typeof this.state.item.id !== 'undefined' ? (
					<React.Fragment>
						<div className='uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default'>
							<div>
								<h2 className="uk-heading-divider">{this.state.item.title}</h2>
							</div>
							<div className="hubloy_membership-membership hubloy_membership-membership-comms">
								<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit} ref={this.comm_setting}>
									<InputUI name={`id`} type={`hidden`} value={this.state.item.id}/>
									<div dangerouslySetInnerHTML={{ __html: this.state.item.form }} />
									<div className="uk-margin">
										<button className="uk-button uk-button-primary update-button">{hubloy_membership.common.buttons.update}</button>
									</div>
								</form>
							</div>
						</div>
					</React.Fragment>
				) : (
					<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
						<span className="uk-text-center" uk-spinner="ratio: 3"></span>
					</div>
				)}
			</Dashboard>
		)
	}
}