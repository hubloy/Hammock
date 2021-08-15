import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import fetchWP from 'utils/fetchWP';

export default class Wizard extends Component {

    constructor(props) {
		super(props);
        this.state = {
			processing : false,
			settings: {},
            pages : {},
            currencies : {}
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	render() {
        var hammock = this.props.hammock;
		return (
			<Dashboard hammock={hammock}>
				<h2 className="uk-text-center uk-heading-divider">{hammock.strings.dashboard.wizard.title}</h2>
                <div className="uk-background-default uk-align-center uk-width-2-3@l uk-width-1-2@m uk-width-1-1@s uk-margin-medium-top uk-padding-small uk-panel uk-height-medium">
				    <progress className="uk-progress uk-width-1-2 uk-align-center" value="10" max="100"></progress>
                    
                </div>
			</Dashboard>
		)
	}
}

Wizard.propTypes = {
	hammock: PropTypes.object
};