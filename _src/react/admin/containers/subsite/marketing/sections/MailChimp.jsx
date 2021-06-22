import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP'
import { CheckBox, InputUI, DropDownUI } from 'ui/admin/form';

export default class MailChimpSettings extends Component {

	constructor(props) {
        super(props);
		
        this.state = {
            settings: {},
            loading : true,
			error : false
        };
        
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });

		this.mailchimp_settings = React.createRef();
		this.handleSubmit = this.handleSubmit.bind(this);
    }

	componentDidMount() {
		this.get_settings();
	}

	get_settings = async () => {
        this.fetchWP.get( 'addons/settings?name=mailchimp' )
            .then( (json) => this.setState({
                settings : json,
                loading : false,
				error : false,
            }), (err) => this.setState({ loading : false, error : true })
        );
    }
	

	handleSubmit( event ) {
        event.preventDefault();

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
				var data = this.state.settings;
				return (
					<form name="hammock-settings-form" className="uk-form-horizontal uk-margin-small" method="POST" onSubmit={this.handleSubmit} ref={this.mailchimp_settings}>
						
						<div className="uk-margin">
							<label className="uk-form-label" htmlFor="apikey">{hammock.strings.mailchimp.apikey}</label>
							<div className="uk-form-controls">
								<div className="uk-grid-collapse uk-child-width-expand@s uk-text-center" uk-grid="">
									<div className="uk-width-expand">
										<div className="uk-inline uk-width-1-1">
											<span className="uk-form-icon uk-form-icon-flip success" uk-icon="icon: check"></span>
											<InputUI name={`apikey`} type={`text`} value={data.apikey} />
										</div>
									</div>
									<div className="uk-width-auto uk-margin-small-left">
										<a className="uk-button uk-button-primary">{hammock.strings.mailchimp.validate}</a>
									</div>
								</div>
								<p className="uk-text-meta" dangerouslySetInnerHTML={{ __html: hammock.strings.mailchimp.info }} />
							</div>
						</div>
						<div className="uk-margin">
							<label className="uk-form-label" htmlFor="apikey">{hammock.strings.mailchimp.enabled}</label>
							<div className="uk-form-controls hammock-input">
								<CheckBox name={`enabled`} value={`1`} checked={data.enabled == 1}/>
							</div>
						</div>
					</form>
				)
			}
		}
	}
}