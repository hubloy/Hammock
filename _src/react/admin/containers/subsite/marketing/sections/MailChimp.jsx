import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP'
import { CheckBox, InputUI, DropDownUI, SwitchUI } from 'ui/admin/form';

export default class MailChimpSettings extends Component {

	constructor(props) {
        super(props);
		
        this.state = {
            settings: {},
            loading : true,
			error : false,
			lists : [],
			valid: false,
			enabled : false
        };
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });

		this.mailchimp_settings = React.createRef();
		this.showOptions = this.showOptions.bind(this);
		this.validateApiKey = this.validateApiKey.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);
    }

	componentDidMount() {
		this.get_settings();
	}

	get_settings = async () => {
        this.fetchWP.get( 'addons/settings?name=mailchimp' )
            .then( (json) => {
				this.setState({
					settings : json,
					loading : false,
					error : false,
					enabled : json.enabled,
					valid : json.valid
				});

				if (json.valid) {
					this.get_lists();
				}
			}, (err) => this.setState({ loading : false, error : true })
        );
    }

	get_lists = async () => {
		this.fetchWP.post( 'addons/action', { id: 'mailchimp', action : 'get_lists' } )
			.then( (json) => {
				if ( json.status ) {
					self.setState({ lists : json.lists });
                } else {
					self.setState({ lists : [] });
                }
			}, (err) => {
				self.setState({ lists : [] });
			}
		);
	}
	

	handleSubmit( event ) {
        event.preventDefault();

	}


	validateApiKey( event ) {
		event.preventDefault();
		var form = this.form_form.current,
			$button = form.querySelectorAll('.submit-button')[0],
			apikey = form.getElementsByName('apikey')[0],
			content = $button.innerHTML,
			self = this,
			hammock = self.props.hammock,
			helper = hammock.helper;
		$button.disabled = true;
		$button.innerHTML = "<div uk-spinner></div>";
		this.fetchWP.post( 'addons/action', { id: 'mailchimp', action : 'check_status', apikey : apikey.value } )
			.then( (json) => {
				if ( json.status ) {
                    helper.alert( this.props.hammock.common.status.success, json.message, 'success');
					self.setState({ valid : true, lists : json.lists });
                } else {
                    helper.alert( this.props.hammock.common.status.error, json.message, 'warning' );
					self.setState({ valid : false });
                }
                $button.disabled = false;
                $button.innerHTML = content;
			}, (err) => {
				$button.disabled = false;
                $button.innerHTML = content;
				helper.alert( this.props.hammock.common.status.error, err.message, 'error' );
				self.setState({ valid : false });
			}
		);
	}

	showOptions( checked ) {
		this.setState({ enabled: checked });
	}

	render() {
        if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
            if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{this.props.hammock.error}</h3>
				)
			} else {
				var hammock = this.props.hammock;
				var data = this.state.settings,
					enabled = this.state.enabled,
					valid = this.state.valid,
					lists = this.state.lists;
				return (
					<form name="hammock-settings-form" className="uk-form-horizontal uk-margin-small" method="POST" onSubmit={this.handleSubmit} ref={this.mailchimp_settings}>
						<div className="uk-margin">
							<label className="uk-form-label" htmlFor="apikey">{hammock.strings.mailchimp.enabled}</label>
							<div className="uk-form-controls hammock-input">
								<CheckBox name={`enabled`} action={this.showOptions} value={`1`} checked={enabled}/>
							</div>
						</div>
						{enabled &&
							<React.Fragment>
								<div className="uk-margin">
									<label className="uk-form-label" htmlFor="apikey">{hammock.strings.mailchimp.apikey}</label>
									<div className="uk-form-controls">
										<div className="uk-grid-collapse uk-child-width-expand@s uk-text-center" uk-grid="">
											<div className="uk-width-expand">
												<div className="uk-inline uk-width-1-1">
													{valid &&
														<span className="uk-form-icon uk-form-icon-flip success" uk-icon="icon: check"></span>
													}
													<InputUI name={`apikey`} type={`text`} value={data.apikey} />
												</div>
											</div>
											<div className="uk-width-auto uk-margin-small-left">
												<a className="uk-button uk-button-primary validation-button" onClick={this.validateApiKey}>{hammock.strings.mailchimp.validate}</a>
											</div>
										</div>
										<p className="uk-text-meta" dangerouslySetInnerHTML={{ __html: hammock.strings.mailchimp.info }} />
									</div>
								</div>
								{valid &&
									<React.Fragment>
										<div className="uk-margin">
											<label className="uk-form-label" htmlFor="double_optin">{hammock.strings.mailchimp.opt_in.label}</label>
											<div className="uk-form-controls">
												<div className="hammock-input">
													<SwitchUI name={`double_optin`} class_name={`double_optin`} title={''} value={`1`} selected={data.double_optin} checked={data.double_optin == 1}/>
													<p className="uk-text-meta">{hammock.strings.mailchimp.opt_in.description}</p>
												</div>
											</div>
										</div>
										<div className="uk-margin">
											<label className="uk-form-label" htmlFor="registered_list">{hammock.strings.mailchimp.lists.registered}</label>
											<div className="uk-form-controls">
												<DropDownUI name={`registered_list`} values={lists} value={data.registered_list} />
											</div>
										</div>
										<div className="uk-margin">
											<label className="uk-form-label" htmlFor="subscriber_list">{hammock.strings.mailchimp.lists.subscriber}</label>
											<div className="uk-form-controls">
												<DropDownUI name={`subscriber_list`} values={lists} value={data.subscriber_list} />
											</div>
										</div>
										<div className="uk-margin">
											<label className="uk-form-label" htmlFor="unsubscriber_list">{hammock.strings.mailchimp.lists.unsubscriber}</label>
											<div className="uk-form-controls">
												<DropDownUI name={`unsubscriber_list`} values={lists} value={data.unsubscriber_list} />
											</div>
										</div>
									</React.Fragment>
								}
							</React.Fragment>
						}
						<div className="uk-margin ">
							<button className="uk-button uk-button-primary save-button">{hammock.common.buttons.save}</button>
						</div>
					</form>
				)
			}
		}
	}
}