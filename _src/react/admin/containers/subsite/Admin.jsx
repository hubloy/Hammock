import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';

import MembersDashboard from './dashboard/members';
import MembershipDashboard from './dashboard/memberships';
import StatsDashboard from './dashboard/stats';

export default class Admin extends Component {
	
	constructor(props) {
		super(props);
	}

	render() {
		
		var hammock = this.props.hammock;
		return (
			<Dashboard hammock={hammock}>
				<div uk-grid="">
					<div className="uk-width-1-1">
						<StatsDashboard hammock={hammock}/>
					</div>
					<div className="uk-width-1-1">
						<div className="uk-grid-small uk-child-width-1-3@m uk-child-width-1-1@s" uk-grid="">
							<div className="">
								<MembersDashboard hammock={hammock} />
							</div>
							<div className="">
								<MembershipDashboard hammock={hammock} />
							</div>
							<div className="">
								<div className="uk-background-default uk-padding-small uk-panel uk-height-medium">
									<p className="uk-h4">{hammock.strings.dashboard.management.title}</p>
									<div>
										{Object.entries(hammock.strings.dashboard.management.types).map((type, index) => {
											return (<a key={index} href={type[1].url}><div className="uk-margin-small uk-padding-small uk-card uk-card-default uk-card-body">{type[1].name}</div></a>)
										})}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</Dashboard>
		)
	}
}

Admin.propTypes = {
	hammock: PropTypes.object
};