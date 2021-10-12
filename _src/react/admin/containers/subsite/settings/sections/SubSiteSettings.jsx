import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP'
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class SubSiteSettings extends Component {

    constructor(props) {
        super(props);
		this.save_sub_site_setting = React.createRef();
        this.state = {
            settings: {},
            pages : {},
            currencies : {},
            loading : true,
			error : false
        };
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
    }

	notify(message, type) {
		toast[type](message, {toastId: 'site-settings-toast'});
	}

    componentDidMount() {
        Promise.all([this.get_settings(), this.get_pages(), this.get_currencies()]);    
    }

    get_settings = async () => {
		this.setState({ loading : true, error : false });
        this.fetchWP.get( 'settings/get' )
            .then( (json) => this.setState({
                settings : json,
                loading : false,
				error : false,
            }), (err) => this.setState({ loading : false, error : true })
        );
    }

    get_pages = async () => {
        this.fetchWP.get( 'settings/pages' )
            .then( (json) => this.setState({
                pages : json
            }), (err) => console.log( 'error', err )
        );
    }

    get_currencies = async () => {
        this.fetchWP.get( 'settings/currencies' )
            .then( (json) => this.setState({
                currencies : json
            }), (err) => console.log( 'error', err )
        ); 
    }

    saveSettings( event ) {
        event.preventDefault();
        var self = this,
            $form = jQuery(self.save_sub_site_setting.current),
            $button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize();
            
        $button.attr('disabled', 'disabled');
        $button.html("<div uk-spinner></div>");
        this.fetchWP.post( 'settings/update', form, true )
            .then( (json) => {
                if ( json.status ) {
                    self.notify( json.message, 'success');
					Promise.all([self.get_settings(), self.get_pages()]); 
                } else {
                    self.notify( json.message, 'warning' );
                }
                $button.removeAttr('disabled');
                $button.html($btn_txt);
            }, (err) => {
                $button.removeAttr('disabled');
                $button.html($btn_txt);
                self.notify( self.props.hammock.error, 'error' );
            }
        );
    }
    
    toggle_protection( checked ) {
		var access = document.getElementsByClassName('hammock-protection-access');
		access = access[0];
		if ( checked ) {
			access.style.display = "block";
		} else {
			access.style.display = "none";
		}
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
				var pages = this.state.settings.pages,
					strings = this.props.hammock.strings;
				return (
					<form name="hammock-settings-form" className="uk-form-horizontal uk-margin-small" method="POST" onSubmit={this.saveSettings.bind(this)} ref={this.save_sub_site_setting}>
						<div id="hammock-before-settings" className="hammock-before-settings"></div>
						<div className="uk-child-width-1-2@s uk-child-width-1-3@m" uk-grid="">
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<div className="hammock-input">
										<SwitchUI name={`content_protection`} class_name={`content_protection`} title={strings.content_protection.title} value={`1`} selected={this.state.settings.content_protection} checked={this.state.settings.content_protection == 1} action={this.toggle_protection.bind(this)}/>
										<p className="uk-text-meta">{strings.content_protection.description}</p>
									</div>
								</div>
							</div>
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<div className="hammock-input">
										<SwitchUI name={`admin_toolbar`} class_name={`admin_toolbar`} title={strings.admin_toolbar.title} value={`1`} selected={this.state.settings.admin_toolbar} checked={this.state.settings.admin_toolbar == 1}/>
										<p className="uk-text-meta">{strings.admin_toolbar.description}</p>
									</div>
								</div>
							</div>
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<div className="hammock-input">
										<SwitchUI name={`account_verification`} class_name={`account_verification`} title={strings.account_verification.title} value={`1`} selected={this.state.settings.account_verification} checked={this.state.settings.account_verification == 1}/>
										<p className="uk-text-meta">{strings.account_verification.description}</p>
									</div>
								</div>
							</div>
						</div>
						<h1 className="uk-heading-divider uk-text-small">{strings.settings.title}</h1>
						<div className="uk-child-width-1-1@s uk-child-width-1-2@m" uk-grid="">
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<label>{strings.settings.currency.title}</label>
									<DropDownUI name={`membership_currency`} values={this.state.currencies} value={this.state.settings.currency}/>
									<p className="uk-text-meta">{strings.settings.currency.description}</p>
								</div>
							</div>
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<label>{strings.settings.invoice.title}</label>
									<InputUI name={`invoice_prefix`} type={`text`} value={typeof this.state.settings.prefix !== 'undefined' ? this.state.settings.prefix : ''}/>
									<p className="uk-text-meta">{strings.settings.invoice.description}</p>
								</div>
							</div>
						</div>
						<div className="hammock-protection-access uk-child-width-1-1@s uk-child-width-1-1@m" uk-grid="" style={{display: ( this.state.settings.content_protection === 1 ? 'block' : 'none' )}}>
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<label>{strings.settings.protection.title}</label>
									<DropDownUI name={`protection_level`} values={strings.settings.protection.options} value={this.state.settings.protection_level !== 'undefined' ? this.state.settings.protection_level : 'hide'}/>
									<p className="uk-text-meta">{strings.settings.protection.description}</p>
								</div>
							</div>
						</div>
						<h1 className="uk-heading-divider uk-text-small">{strings.pages.title}</h1>
						<div className="uk-child-width-1-1@s uk-child-width-1-2@m uk-child-width-1-3@l" uk-grid="">
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<label>{strings.pages.membership_list.title}</label>
									<DropDownUI name={`membership_list`} values={this.state.pages} value={typeof pages.membership_list !== 'undefined' ? pages.membership_list : 0 } />
									<p className="uk-text-meta">{strings.pages.membership_list.description}</p>
								</div>
							</div>
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<label>{strings.pages.protected_content.title}</label>
									<DropDownUI name={`protected_content`} values={this.state.pages} value={typeof pages.protected_content !== 'undefined' ? pages.protected_content : 0 } />
									<p className="uk-text-meta">{strings.pages.protected_content.description}</p>
								</div>
							</div>
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<label>{strings.pages.account_page.title}</label>
									<DropDownUI name={`account_page`} values={this.state.pages} value={typeof pages.account_page !== 'undefined' ? pages.account_page : 0 } />
									<p className="uk-text-meta">{strings.pages.account_page.description}</p>
								</div>
							</div>
						</div>
						<h1 className="uk-heading-divider uk-text-small">{strings.data.title}</h1>
						<div className="uk-child-width-1-1" uk-grid="">
							<div>
								<div className="uk-height-small uk-card uk-card-default uk-card-body uk-padding-small">
									<div className="hammock-input">
										<SwitchUI name={`delete_on_uninstall`} class_name={`delete_on_uninstall`} title={strings.data.delete_on_uninstall.title} value={`1`} selected={this.state.settings.delete_on_uninstall} checked={this.state.settings.delete_on_uninstall == 1}/>
										<p className="uk-text-meta">{strings.data.delete_on_uninstall.description}</p>
									</div>
								</div>
							</div>
						</div>
						<div id="hammock-after-settings" className="hammock-after-settings"></div>
						<div className="uk-margin ">
							<button className="uk-button uk-button-primary save-button">{this.props.hammock.common.buttons.save}</button>
						</div>
					</form>
				)
			}
        }
    }
}
SubSiteSettings.propTypes = {
	hammock: PropTypes.object
};