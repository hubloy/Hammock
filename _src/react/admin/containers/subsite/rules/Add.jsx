import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import { DropDownUI } from 'ui/admin/form';

import fetchWP from 'utils/fetchWP';
import Dashboard from 'layout/Dashboard'

export default class CreateRule extends Component {

	constructor(props) {
		super(props);

		this.state = {
			items : [],
			loading : true,
			error : false
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});

		this.handleSubmit = this.handleSubmit.bind(this);
		this.rule_form = React.createRef();
	}

	async componentDidMount() {
		this.load_options();
	}

	load_options = async () => {
		this.fetchWP.get( 'rules/list' )
			.then( (json) => this.setState({
				items : json,
				loading : false
			}), (err) => this.setState({ loading : false })
		);
	}

	handleSubmit(event) {
		event.preventDefault();

	}

	render() {
		
		var self = this,
			hammock = self.props.hammock,
			strings = hammock.strings;
		return (
			<Dashboard hammock={hammock}>
				{this.state.loading ? (
					<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
						<span className="uk-text-center" uk-spinner="ratio: 3"></span>
					</div>
				) : (
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit} ref={this.rule_form}>
						
						<div className="uk-margin">
							<button className="uk-button uk-button-primary save-button">{hammock.common.buttons.save}</button>
						</div>
					</form>
				)}
			</Dashboard>
		)
	}
}