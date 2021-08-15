import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import fetchWP from 'utils/fetchWP';

import WizardSettings from './wizard/Settings';
import WizardCreateMembership from './wizard/Membership';

export default class Wizard extends Component {

    constructor(props) {
		super(props);
        this.state = {
			loading : true,
			step : { value : 10, step : 'options' },
			currency : this.props.hammock.common.currency_code
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });

        this.buttonClick = this.buttonClick.bind(this);
	}

	componentDidMount() {
        this.get_step();
    }

	get_step = async () => {
        this.fetchWP.get( 'wizard/step' )
            .then( (json) => this.setState({
                step : json,
				loading : false
            }), (err) => console.log( 'error', err )
        );
    }

	buttonClick( step, currency ) {
		this.setState({ step : step, currency : currency });
    }

	render() {
        var hammock = this.props.hammock,
			step = this.state.step;
		return (
			<Dashboard hammock={hammock}>
				<h2 className="uk-text-center uk-heading-divider">{hammock.strings.dashboard.wizard.title}</h2>
                <div className="uk-background-default uk-align-center uk-width-2-3@l uk-width-1-2@m uk-width-1-1@s uk-margin-medium-top uk-padding-small">
				    <progress className="uk-progress uk-width-1-2 uk-align-center" value={step.value} max="100"></progress>
					{this.state.loading ? (
						<div className="uk-container hammock-preloader uk-padding-small uk-align-center uk-margin-top uk-width-1-1 uk-background-default">
							<span className="uk-text-center" uk-spinner="ratio: 3"></span>
						</div>
					) : (
						<React.Fragment>
							{
								{
									'options': <WizardSettings hammock={hammock} action={this.buttonClick}/>,
									'membership': <WizardCreateMembership hammock={hammock} currency={this.state.currency} action={this.buttonClick}/>
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
	hammock: PropTypes.object
};