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
		var hammock = this.props.hammock;
		var section = this.props.match.params.page !== undefined ? this.props.match.params.page : 'mailchimp';
		return (
			<Dashboard hammock={hammock}>
				<div className="hammock-settings uk-width-expand">
					<nav className="uk-navbar-container uk-navbar-transparent" uk-navbar="">
						<div className="uk-navbar-left">
							<ul className="uk-navbar-nav hammock-navbar">
								{Object.entries(hammock.strings.nav).map((type, index) => {
									return (<li key={index} className={section === type[0] ? 'uk-active' : '' }>
										<Link to={"/" + type[0]}><span>{type[1]}</span></Link>
									</li>)
								})}
							</ul>
						</div>
					</nav>
					<div className="hammock-settings-settings hammock-marketing-settings uk-background-default uk-padding-small">
						{
							{
								'mailchimp': <MailChimpSettings hammock={this.props.hammock}/>
							}[section]
						}
					</div>
				</div>
			</Dashboard>
		)
	}
}

Marketing.propTypes = {
	hammock: PropTypes.object
};