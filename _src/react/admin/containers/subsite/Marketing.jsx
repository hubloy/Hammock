import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import Dashboard from 'layout/Dashboard';

import MailChimpSettings from './marketing/sections/MailChimp';

export default class Marketing extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership;
		var section = this.props.match.params.page !== undefined ? this.props.match.params.page : 'mailchimp';
		return (
			<Dashboard hubloy_membership={hubloy_membership}>
				<div className="hubloy_membership-settings uk-width-expand">
					<nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
						<div className="uk-navbar-left">
							<ul className="uk-navbar-nav hubloy_membership-navbar">
								{Object.entries(hubloy_membership.strings.nav).map((type, index) => {
									return (<li key={index} className={section === type[0] ? 'uk-active' : '' }>
										<Link to={"/" + type[0]}><span>{type[1]}</span></Link>
									</li>)
								})}
							</ul>
						</div>
					</nav>
					<div className="hubloy_membership-settings-settings hubloy_membership-marketing-settings uk-background-default uk-padding-small">
						{
							{
								'mailchimp': <MailChimpSettings hubloy_membership={this.props.hubloy_membership}/>
							}[section]
						}
					</div>
				</div>
			</Dashboard>
		)
	}
}

Marketing.propTypes = {
	hubloy_membership: PropTypes.object
};