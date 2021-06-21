import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import fetchWP from 'utils/fetchWP';

import {Preloader, Center} from 'ui/admin/form';

export default class MembershipDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading : true,
			memberships : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	componentDidMount() {
		this.load_data();
	}

	load_data = async() => {
		this.fetchWP.get( 'dashboard/memberships' )
			.then( (json) => this.setState({
				memberships : json,
				loading : false,
				error : false,
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	render() {
		var memberships = this.state.memberships;
		var hammock = this.props.hammock;
		return (
			<div className="uk-background-default uk-padding-small uk-margin-medium-top uk-panel uk-height-medium">
				{this.state.loading ? (
					<Preloader />
				) : (
					<React.Fragment>
						<p className="uk-h4">{hammock.strings.dashboard.memberships.title} <span className="hammock-badge-circle">{memberships.length}</span></p>
						<div>
							{memberships.length <= 0 ? (
								<Center text={hammock.strings.dashboard.memberships.none} className="uk-text-info" />
							) : (
								<ul className="uk-list uk-list-striped">
									{memberships.map(item =>
										<li key={item.id}><a className="uk-text-primary" href={hammock.strings.dashboard.memberships.url + "#/edit/" + item.id}>{item.name}</a></li>
									)}
								</ul>
							)}
						</div>
					</React.Fragment>
				)}
				
			</div>
		)
	}
}