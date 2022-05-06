import React, { Component } from 'react';

import fetchWP from 'utils/fetchWP'
import { InputUI, DropDownUI, SwitchUI } from 'ui/admin/form';
import { toast } from 'react-toastify';

export default class MailChimpSettings extends Component {

	constructor(props) {
        super(props);
		
        this.state = {
            settings: {},
            loading : true,
			error : false,
			lists : [],
			valid: false,
			enabled : false,
			loading_lists : true
        };
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });

		this.mailchimp_settings = React.createRef();
		this.showOptions = this.showOptions.bind(this);
		this.validateApiKey = this.validateApiKey.bind(this);
		this.handleSubmit = this.handleSubmit.bind(this);

		this.get_lists = this.get_lists.bind(this);
    }

	notify(type, message) {
		toast[type](message, {toastId: 'marketing-mailchimp-toast'});
	}

	componentDidMount() {
		this.get_settings();
	}

	get_settings = async () => {
        this.fetchWP.get( 'addons/settings?name=mailchimp' )
            .then( (json) => {
				this.setState({
					settings : json.settings,
					loading : false,
					error : false,
					enabled : json.enabled
				});

				if (json.settings.valid) {
					this.get_lists();
				}
			}, (err) => this.setState({ loading : false, error : true })
        );
    }

	get_lists = async () => {
		this.setState({ loading_lists : true });
		this.fetchWP.post( 'addons/action', { id: 'mailchimp', action : 'get_lists' } )
			.then( (json) => {
				if ( json.success ) {
					this.setState({ lists : json.lists, loading_lists : false });
                } else {
					this.setState({ lists : [], loading_lists : false });
					this.notify( this.props.hubloy_membership.error, 'error' );
                }
			}, (err) => {
				this.setState({ lists : [], loading_lists : false });
				this.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}
	

	handleSubmit( event ) {
		event.preventDefault();
        var self = this,
			$form = jQuery(self.mailchimp_settings.current),
			$button = $form.find('.submit-button'),
			$btn_txt = $button.text(),
			form = $form.serialize(),
			hubloy_membership = this.props.hubloy_membership;
		$button.attr('disabled', 'disabled');
		$button.html("<div uk-spinner></div>");
		this.fetchWP.post( 'addons/settings/update', form, true )
			.then( (json) => {
				console.log(json);
				if ( json.status ) {
					self.notify( 'info', json.message );
				} else {
					self.notify( 'warning', json.message );
				}
				$button.removeAttr('disabled');
				$button.html($btn_txt);
			}, (err) => {
				$button.removeAttr('disabled');
				$button.html($btn_txt);
				self.notify( 'error', hubloy_membership.error );
			}
		);
	}


	validateApiKey( event ) {
		event.preventDefault();
		var form = this.mailchimp_settings.current,
			$button = form.querySelectorAll('.submit-button')[0],
			apikey = form.querySelectorAll('.apikey')[0],
			content = $button.innerHTML,
			self = this,
			hubloy_membership = self.props.hubloy_membership;
		$button.disabled = true;
		$button.innerHTML = "<div uk-spinner></div>";
		this.fetchWP.post( 'addons/action', { id: 'mailchimp', action : 'check_status', apikey : apikey.value } )
			.then( (json) => {
				if ( json.success ) {
                    self.notify( 'info', json.message );
					self.setState({ valid : true, lists : json.lists, loading_lists : false });
                } else {
                    self.notify( 'warning', json.message );
					self.setState({ valid : false });
                }
                $button.disabled = false;
                $button.innerHTML = content;
			}, (err) => {
				$button.disabled = false;
                $button.innerHTML = content;
				self.notify( 'error', hubloy_membership.error );
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
					<h3 className="uk-text-center uk-text-danger">{this.props.hubloy_membership.error}</h3>
				)
			} else {
				var hubloy_membership = this.props.hubloy_membership;
				var data = this.state.settings,
					enabled = this.state.enabled,
					lists = this.state.lists,
					loading_lists = this.state.loading_lists;
				return (
					<form name="hubloy_membership-settings-form" className="uk-form-horizontal uk-margin-small" method="POST" onSubmit={this.handleSubmit} ref={this.mailchimp_settings}>
						<InputUI name={`id`} class_name={`addon_id`} type={`hidden`} value='mailchimp'/>
						<div className="uk-margin">
							<label className="uk-form-label" htmlFor="apikey">{hubloy_membership.strings.mailchimp.title}</label>
							<div className="uk-form-controls hubloy_membership-input">
								<SwitchUI name={`enabled`} action={this.showOptions} value={`1`} checked={enabled} enabled_title={hubloy_membership.common.status.enabled} title={hubloy_membership.common.status.disabled}/>
								<p className="uk-text-meta">{hubloy_membership.strings.mailchimp.desc}</p>
							</div>
						</div>
						{enabled &&
							<React.Fragment>
								<div className="uk-margin">
									<label className="uk-form-label" htmlFor="apikey">{hubloy_membership.strings.mailchimp.apikey}</label>
									<div className="uk-form-controls">
										<div className="uk-grid-collapse uk-child-width-expand@s uk-text-center" uk-grid="">
											<div className="uk-width-expand">
												<div className="uk-inline uk-width-1-1">
													{data.valid &&
														<span className="uk-form-icon uk-form-icon-flip success" uk-icon="icon: check"></span>
													}
													<InputUI name={`apikey`} type={`text`} value={data.apikey} />
												</div>
											</div>
											<div className="uk-width-auto uk-margin-small-left">
												<a className="uk-button uk-button-primary validation-button" onClick={this.validateApiKey}>{hubloy_membership.strings.mailchimp.validate}</a>
											</div>
										</div>
										<p className="uk-text-meta" dangerouslySetInnerHTML={{ __html: hubloy_membership.strings.mailchimp.info }} />
									</div>
								</div>
								{data.valid &&
									<React.Fragment>
										<div className="uk-margin">
											<label className="uk-form-label" htmlFor="double_optin">{hubloy_membership.strings.mailchimp.opt_in.label}</label>
											<div className="uk-form-controls">
												<div className="hubloy_membership-input">
													<SwitchUI name={`double_optin`} class_name={`double_optin`} title={''} value={`1`} selected={data.double_optin} checked={data.double_optin == 1}/>
													<p className="uk-text-meta">{hubloy_membership.strings.mailchimp.opt_in.description}</p>
												</div>
											</div>
										</div>
										{loading_lists ? (
											<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
												<span className="uk-text-center" uk-spinner="ratio: 3"></span>
											</div>
										) : (
											<React.Fragment>
												<div className="uk-margin">
													<label className="uk-form-label" htmlFor="registered_list">{hubloy_membership.strings.mailchimp.lists.registered}</label>
													<div className="uk-form-controls">
														<DropDownUI name={`registered_list`} values={lists} value={data.registered_list} />
													</div>
												</div>
												<div className="uk-margin">
													<label className="uk-form-label" htmlFor="subscriber_list">{hubloy_membership.strings.mailchimp.lists.subscriber}</label>
													<div className="uk-form-controls">
														<DropDownUI name={`subscriber_list`} values={lists} value={data.subscriber_list} />
													</div>
												</div>
												<div className="uk-margin">
													<label className="uk-form-label" htmlFor="unsubscriber_list">{hubloy_membership.strings.mailchimp.lists.unsubscriber}</label>
													<div className="uk-form-controls">
														<DropDownUI name={`unsubscriber_list`} values={lists} value={data.unsubscriber_list} />
													</div>
												</div>
											</React.Fragment>
										)}
									</React.Fragment>
								}
							</React.Fragment>
						}
						<div className="uk-margin ">
							<button className="uk-button uk-button-primary submit-button">{hubloy_membership.common.buttons.save}</button>
						</div>
					</form>
				)
			}
		}
	}
}