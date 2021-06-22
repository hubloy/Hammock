import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import Dashboard from 'layout/Dashboard';
import Nav from './marketing/Nav';

import MailChimpSettings from './marketing/sections/MailChimp';

export default class Marketing extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hammock = this.props.hammock;
		var section = this.props.match.params.page !== undefined ? this.props.match.params.page : 'mailchimp';
		return (
			<Dashboard hammock={hammock}>
				<div uk-grid="">
					<LazyLoad className="uk-width-1-4@m uk-width-1-1@s uk-height-medium">
						<Nav hammock={this.props.hammock} active_nav={section}/>
					</LazyLoad>
					
					<div className="hammock-settings uk-width-expand uk-margin-left uk-card uk-card-body">
						<div className="hammock-settings-settings hammock-marketing-settings uk-background-default uk-padding-small uk-border-rounded">
							{
								{
									'mailchimp': <MailChimpSettings hammock={this.props.hammock}/>
								}[section]
							}
						</div>
					</div>
				</div>
			</Dashboard>
		)
	}
}

Marketing.propTypes = {
	hammock: PropTypes.object
};