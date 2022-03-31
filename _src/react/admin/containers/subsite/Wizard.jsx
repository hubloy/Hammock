import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import fetchWP from 'utils/fetchWP';

import WizardSettings from './wizard/Settings';
import WizardCreateMembership from './wizard/Membership';

import { toast } from 'react-toastify';


export default class Wizard extends Component {

    constructor(props) {
		super(props);
        this.state = {
			loading : true,
			step : { value : 10, step : 'options' },
			currency : this.props.hubloy_membership.common.currency_code
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });

        this.buttonClick = this.buttonClick.bind(this);
	}

	notify(type, message) {
		toast[type](message, {toastId: 'wizard-toast'});
	}

	componentDidMount() {
        this.get_step();
    }

	get_step = async () => {
        this.fetchWP.get( 'wizard/step' )
            .then( (json) => this.setState({
                step : json,
				loading : false
            }), (err) => {
				this.notify( this.props.hubloy_membership.error, 'error' );
				this.setState({loading : false});
			}
        );
    }

	buttonClick( step, currency ) {
		this.setState({ step : step, currency : currency });
    }

	render() {
        var hubloy_membership = this.props.hubloy_membership,
			step = this.state.step;
		return (
			<Dashboard hubloy_membership={hubloy_membership}>
				<h2 className="uk-text-center uk-heading-divider">{hubloy_membership.strings.dashboard.wizard.title}</h2>
                <div className="uk-background-default uk-align-center uk-width-2-3@l uk-width-1-2@m uk-width-1-1@s uk-margin-medium-top uk-padding-small">
				    <progress className="uk-progress uk-width-1-2 uk-align-center" value={step.value} max="100"></progress>
					{this.state.loading ? (
						<div className="uk-container hubloy_membership-preloader uk-padding-small uk-align-center uk-margin-top uk-width-1-1 uk-background-default">
							<span className="uk-text-center" uk-spinner="ratio: 3"></span>
						</div>
					) : (
						<React.Fragment>
							{
								{
									'options': <WizardSettings hubloy_membership={hubloy_membership} action={this.buttonClick}/>,
									'membership': <WizardCreateMembership hubloy_membership={hubloy_membership} currency={this.state.currency} action={this.buttonClick}/>
								}[step.step]
							}
						</React.Fragment>
					)}
                </div>
			</Dashboard>
		)
	}
}

Wizard.propTypes = {
	hubloy_membership: PropTypes.object
};