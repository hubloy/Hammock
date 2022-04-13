import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP'
import { InputUI } from 'ui/admin/form'

import { toast } from 'react-toastify';

export default class SubSiteDefault extends Component {

    constructor(props) {
        super(props);
		this.save_sub_default_setting = React.createRef();
        this.state = {
            loading : true,
			error : false,
            form : ''
        };
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
    }

    componentDidMount() {
		const section = this.props.section;
		this.fetchWP.get( 'settings/section/get/' + section )
			.then( (json) => this.setState({
				form : json.form,
                loading : false,
                error : false
			}), (err) => this.setState({ loading : false, error : true})
		);
	}

	notify(message, type) {
		toast[type](message, {toastId: 'site-default-toast'});
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

		this.fetchWP.post( 'settings/section/update', form, true )
			.then( (json) => {
				if ( json.status ) {
					self.setState({
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
				self.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

    render() {
        const { section, hubloy_membership } = this.props;
        if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
            if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hubloy_membership.error}</h3>
				)
			} else {
                return (
					<form className="uk-form-horizontal uk-margin-large" onSubmit={this.handleSubmit.bind(this)} ref={this.save_sub_default_setting}>
						<InputUI name={`section`} type={`hidden`} value={section}/>
						<div className={"hubloy_membership_default_settings hubloy_membership_default_settings-" + section} dangerouslySetInnerHTML={{ __html: this.state.form }}></div>
						<div className="uk-margin">
							<button className="uk-button uk-button-primary update-button">{hubloy_membership.common.buttons.save}</button>
						</div>
					</form>
                )
			}
		}
    }
}
SubSiteDefault.propTypes = {
	hubloy_membership: PropTypes.object
};