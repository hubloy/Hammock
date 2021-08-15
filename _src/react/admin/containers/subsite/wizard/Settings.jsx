import React, { PureComponent } from 'react';
import { SwitchUI, InputUI, DropDownUI } from 'ui/admin/form';
import fetchWP from 'utils/fetchWP';

export default class WizardSettings extends PureComponent {
    constructor(props) {
		super(props);
        this.state = {
			loading_pages : true,
			loading_currencies : true,
			processing : false,
			settings: this.props.settings,
            pages : {},
            currencies : {}
        };

        this.setting_form = React.createRef();

        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });

        this.saveSettings = this.saveSettings.bind(this);
	}

	componentDidMount() {
        Promise.all([this.get_settings(), this.get_pages(), this.get_currencies()]);    
    }

    get_settings = async () => {
        this.fetchWP.get( 'settings/get' )
            .then( (json) => this.setState({
                settings : json,
                loading_settings : false,
				error : false,
            }), (err) => console.log( 'error', err )
        );
    }

    get_pages = async () => {
        this.fetchWP.get( 'settings/pages' )
            .then( (json) => this.setState({
                pages : json,
				loading_pages : false
            }), (err) => console.log( 'error', err )
        );
    }

    get_currencies = async () => {
        this.fetchWP.get( 'settings/currencies' )
            .then( (json) => this.setState({
                currencies : json,
				loading_currencies : false
            }), (err) => console.log( 'error', err )
        ); 
    }

    saveSettings( event ) {
        event.preventDefault();
        var self = this,
            $form = jQuery(self.setting_form.current),
            $button = $form.find('button'),
			$btn_txt = $button.text(),
			form = $form.serialize(),
            helper = window.hammock.helper,
            action = this.props.action;
            
        $button.attr('disabled', 'disabled');
        $button.html("<div uk-spinner></div>");
        this.fetchWP.post( 'wizard/settings', form, true )
            .then( (json) => {
                if ( json.status ) {
                    helper.alert( this.props.hammock.common.status.success, json.message, 'success');
					action( json.data );
                } else {
                    helper.alert( this.props.hammock.common.status.error, json.message, 'warning' );
                }
                $button.removeAttr('disabled');
                $button.html($btn_txt);
            }, (err) => {
                $button.removeAttr('disabled');
                $button.html($btn_txt);
                helper.alert( this.props.hammock.common.status.error, this.props.hammock.error, 'error' );
            }
        );
    }

    render() {
        var hammock = this.props.hammock,
            pages = this.state.settings.pages,
            strings = hammock.strings.dashboard.wizard;
        return (
            <form name="hammock-settings-form" className="uk-form-horizontal uk-margin-small" method="POST" onSubmit={this.saveSettings} ref={this.setting_form}>
                <div className="uk-margin">
                    <label>{strings.settings.currency.title}</label>
                    <DropDownUI name={`membership_currency`} values={this.state.currencies} value={this.state.settings.currency}/>
                    <p className="uk-text-meta">{strings.settings.currency.description}</p>
                </div>
                <h1 className="uk-heading-divider uk-text-small">{strings.pages.title}</h1>
                <div className="uk-margin">
                    <label>{strings.pages.membership_list.title}</label>
                    <DropDownUI name={`membership_list`} values={this.state.pages} value={typeof pages.membership_list !== 'undefined' ? pages.membership_list : 0 } />
                    <p className="uk-text-meta">{strings.pages.membership_list.description}</p>
                </div>
                <div className="uk-margin">
                    <label>{strings.pages.protected_content.title}</label>
                    <DropDownUI name={`protected_content`} values={this.state.pages} value={typeof pages.protected_content !== 'undefined' ? pages.protected_content : 0 } />
                    <p className="uk-text-meta">{strings.pages.protected_content.description}</p>
                </div>
                <div className="uk-margin">
                    <label>{strings.pages.account_page.title}</label>
                    <DropDownUI name={`account_page`} values={this.state.pages} value={typeof pages.account_page !== 'undefined' ? pages.account_page : 0 } />
                    <p className="uk-text-meta">{strings.pages.account_page.description}</p>
                </div>
                <div className="uk-margin ">
                    <button className="uk-button uk-button-primary save-button">{hammock.common.buttons.save}</button>
                </div>
            </form>
        )
    }
}