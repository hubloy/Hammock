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
			items : '',
			loading : false,
			error : false,
			membership : '',
			membership_loading : false,
			checked: false
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});
		this.onChange = this.onChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
		this.load_memberships = this.load_memberships.bind(this);
		this.handleTypeSelect = this.handleTypeSelect.bind(this);
		this.load_items = this.load_items.bind(this);
		this.rule_form = React.createRef();
	}

	onChange(e) {
		this.setState({ checked: !!e.target.checked });
	}

	load_items = async ( type ) => {
		this.fetchWP.get( 'rules/items/' + type )
			.then( (json) => {
				this.setState({
					items : json,
					loading : false
				});
				hammock.helper.select2();
			}, (err) => this.setState({ loading : false })
		);
	}

	load_memberships = async () => {
		this.fetchWP.get( 'rules/memberships' )
			.then( (json) => {
				this.setState({
					membership : json,
					membership_loading : false
				});
				hammock.helper.select2();
			}, (err) => this.setState({ membership_loading : false })
		);
	}

	handleTypeSelect( target, value ) {
		if ( value != '0' ) {
			this.setState({ type : value, membership_loading : true, loading : true });
			Promise.all([this.load_items( value ), this.load_memberships()]);
		} else {
			this.setState({ type : false, membership : '', items : '' });
		}
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
									<DropDownUI name={`type`} values={self.props.rules} value={self.state.type} action={this.handleTypeSelect} blank={true}/>
								</div>
							</div>
							
							{self.state.loading ? (
								<span className="uk-text-center" uk-spinner="ratio: 2"></span>
							) : (
								self.state.type &&
									<div className="uk-margin">
										<legend className="uk-form-label">{strings.item}</legend>
										<div className="uk-form-controls" dangerouslySetInnerHTML={{ __html: self.state.items }}></div>
									</div>
							)}
							{self.state.membership_loading ? (
								<span className="uk-text-center" uk-spinner="ratio: 2"></span>
							) : (
								self.state.type &&
									<div className="uk-margin">
										<legend className="uk-form-label">{strings.membership}</legend>
										<div className="uk-form-controls" dangerouslySetInnerHTML={{ __html: self.state.membership }}></div>
									</div>

							)}
							{self.state.type &&
								<React.Fragment>
									<div className="uk-margin">
										<legend className="uk-form-label">{hammock.common.status.status}</legend>
										<div className="uk-form-controls">
											<div className="hammock-input">
												<section className="slider-checkbox">
													<input
														type="checkbox"
														name={'enabled'}
														checked={this.state.checked}
														value={`1`} 
														onChange={this.onChange}/>
													<label className='label' htmlFor={'enabled'}>
														{this.state.checked ? hammock.common.status.enabled : hammock.common.status.disabled }
													</label>
												</section>
											</div>
										</div>
									</div>
									<div className="uk-margin">
										<button className="uk-button uk-button-primary save-button">
											{self.state.loading || self.state.membership_loading ? (
												<div uk-spinner=""></div>
											) : (
												hammock.common.buttons.save
											)}
										</button>
									</div>
								</React.Fragment>
							}
						</form>
					</div>
				</div>
			</div>
		)
	}
}