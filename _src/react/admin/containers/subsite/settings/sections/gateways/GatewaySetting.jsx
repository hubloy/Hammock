import React, { PureComponent } from 'react';

import fetchWP from 'utils/fetchWP';
import { InputUI } from 'ui/admin/form'

import { toast } from 'react-toastify';

export default class GatewaySetting extends PureComponent {

	constructor(props) {
		super(props);
		this.gateway_setting = React.createRef();
		this.state = {
			item : undefined,
			checked: false,
			settings : {}
		};
		this.onChange = this.onChange.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	onChange(e) {
		this.setState({ checked: !!e.target.checked });
	}

	notify(type, message) {
		toast[type](message, {toastId: 'site-gateway-toast'});
	}

	componentDidMount() {
		const id = this.props.id;
		this.fetchWP.get( 'gateways/settings?id=' + id )
			.then( (json) => this.setState({
				item : json.form,
				settings : json.settings,
				checked : json.settings.enabled
			}), (err) => console.log( 'error', err )
		);
	}

	handleSubmit(event) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.gateway_setting.current),
			$button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();

		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");

		this.fetchWP.post( 'gateways/update', form, true )
			.then( (json) => {
				if ( json.status ) {
					this.setState({
						settings : json.settings
					});
					self.notify( json.message, 'success' );
				} else {
					self.notify( json.message, 'warning' );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( this.props.hammock.error, 'error' );
			}
		);
	}

	render() {
		const enabled = this.state.settings.enabled !== 'undefined' ? this.state.settings.enabled : false;
		var hammock = this.props.hammock;
		return (
			<li className="uk-background-default uk-padding-small" key={this.props.id}>
				<span className="uk-accordion-title hammock-pointer" href="#">{this.props.item.name} <span className={ "uk-label uk-text-uppercase uk-text-small hammock-gateway-status " + (enabled ? 'uk-label-success' : '') }>{enabled ? hammock.common.status.enabled : hammock.common.status.disabled}</span></span>
				<div className="uk-accordion-content">
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.gateway_setting}>
						<InputUI name={`id`} type={`hidden`} value={this.props.id}/>
						<div className="uk-margin">
							<legend className="uk-form-label">{hammock.common.status.status}</legend>
							<div className="uk-form-controls">
								<div className="hammock-input">
									<section className="slider-checkbox">
										<input
											type="checkbox"
											name={this.props.id}
											value={`1`}
											checked={this.state.checked}
											onChange={this.onChange}
										/>
										<label className='label' htmlFor={this.props.id}>
											{this.state.checked ? hammock.common.status.enabled : hammock.common.status.disabled }
										</label>
									</section>
								</div>
							</div>
						</div>
						<div className="hammock-gateway-settings" dangerouslySetInnerHTML={{ __html: this.state.item }}></div>
						<div className="uk-margin">
							<button className="uk-button uk-button-primary update-button">{hammock.common.buttons.update}</button>
						</div>
					</form>
				</div>
			</li>
		)
	}
}