import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from 'utils/fetchWP';

export default class MembersDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading : true,
			members : []
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
		this.fetchWP.get( 'dashboard/members' )
			.then( (json) => this.setState({
				members : json,
				loading : false,
				error : false,
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	render() {
		return (
			<div className="uk-background-default uk-padding-small uk-panel uk-height-medium">
				<p className="uk-h4">Members <span className="hammock-badge-circle">100</span></p>
				<div>
					<ul className="uk-list uk-list-striped">
						<li>Member 1</li>
						<li>Member 2</li>
						<li>Member 3</li>
						<li>Member 4</li>
					</ul>
				</div>
			</div>
		)
	}
}