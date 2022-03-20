import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import { DropDownUI } from 'ui/admin/form';

import fetchWP from 'utils/fetchWP';

export default class CreateRuleModal extends Component {

	constructor(props) {
		super(props);

		this.state = {
			type : false,
			items : [],
			loading : true,
			error : false,
			membership : '',
			membership_loading : true
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});

		this.handleSubmit = this.handleSubmit.bind(this);
		this.handleTypeSelect = this.handleTypeSelect.bind(this);
		this.load_memberships = this.load_memberships.bind(this);
		this.load_items = this.load_items.bind(this);
		this.rule_form = React.createRef();
	}

	async componentDidUpdate( prevProps ) {
		this.setState({ type : false, membership : '', membership_loading : false, loading : false, items : [], error : false });
	}

	load_items = async ( type ) => {
		this.fetchWP.get( 'rules/items' )
			.then( (json) => this.setState({
				items : json,
				loading : false
			}), (err) => this.setState({ loading : false })
		);
	}

	load_memberships = async ( type ) => {
		this.fetchWP.get( 'rules/dropdown/' + type )
			.then( (json) => this.setState({
				membership : json,
				membership_loading : false
			}), (err) => this.setState({ membership_loading : false })
		);
	}

	handleTypeSelect( target, value ) {
		this.setState({ type : value, membership_loading : true, loading : true });
		Promise.all([this.load_items( value ), this.load_memberships( value )]);
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
						<form className="uk-form-stacked uk-margin-large" onSubmit={self.handleSubmit} ref={self.rule_form}>
							<div className="uk-margin">
								<legend className="uk-form-label">{strings.rule}</legend>
								<div className="uk-form-controls">
									<DropDownUI name={`type`} values={self.props.rules} value={self.state.type} action={self.handleTypeSelect}/>
								</div>
							</div>
							{self.state.loading ? (
								<span className="uk-text-center" uk-spinner="ratio: 2"></span>
							) : (
								<div className="uk-margin">
									<legend className="uk-form-label">{strings.item}</legend>
									<div className="uk-form-controls">
										<DropDownUI name={`id`} values={self.state.items} value={''}/>
									</div>
								</div>
							)}
							{self.state.membership_loading ? (
								<span className="uk-text-center" uk-spinner="ratio: 2"></span>
							) : (
								<div className="uk-margin">
									<legend className="uk-form-label">{strings.item}</legend>
									<div className="uk-form-controls" dangerouslySetInnerHTML={{ __html: self.state.membership }}></div>
								</div>
							)}
							<div className="uk-margin">
								<button className="uk-button uk-button-primary save-button">
									{self.state.loading || self.state.membership_loading ? (
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