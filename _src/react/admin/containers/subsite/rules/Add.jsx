import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import { DropDownUI } from 'ui/admin/form';

import fetchWP from 'utils/fetchWP';

export default class CreateRuleModal extends Component {

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

	load_items = async () => {
		this.fetchWP.get( 'rules/list' )
			.then( (json) => this.setState({
				items : json,
				loading : false
			}), (err) => this.setState({ loading : false })
		);
	}

	load_memberships = async () => {
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
			strings = hammock.strings.dashboard.add_new.modal;
		return (
			<div id="hammock-add-rule" uk-modal="">
				<div className="uk-modal-dialog">
					<button className="uk-modal-close-default" type="button" uk-close=""></button>
					<div className="uk-modal-header">
						<h2 className="uk-modal-title">{strings.title}</h2>
					</div>
					<div className="uk-modal-body">
						<form className="uk-form-stacked uk-margin-large" onSubmit={this.handleSubmit} ref={this.rule_form}>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.rule}</legend>
								<div className="uk-form-controls">
									<DropDownUI name={`type`} values={this.props.rules} value={''}/>
								</div>
							</div>
							{this.state.loading ? (
								<span className="uk-text-center" uk-spinner="ratio: 2"></span>
							) : (
								<div className="uk-margin">
									<legend className="uk-form-label">{strings.item}</legend>
									<div className="uk-form-controls">
										<DropDownUI name={`id`} values={self.state.items} value={''}/>
									</div>
								</div>
							)}
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.membership}</legend>
								<div className="uk-form-controls">
									<DropDownUI name={`membership`} class_name='hammock-chosen-select' values={this.props.rules} value={''}/>
								</div>
							</div>
							<div className="uk-margin">
								<button className="uk-button uk-button-primary save-button">
									{this.state.loading ? (
										<div uk-spinner=""></div>
									) : (
										hammock.common.buttons.save
									)}
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		)
	}
}