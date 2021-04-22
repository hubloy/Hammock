import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from './layout/Dashboard'
import { Filter } from './members/Filter';
import Table from './members/Table';
import {Create} from './members/Create';
import fetchWP from '../../../utils/fetchWP'

export default class Members extends Component {
	constructor(props) {
		super(props);

		this.state = {
			total : 0,
			loading : true
		};
		
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	async componentDidMount() {
		this.count_members()
	}

	count_members = async () => {
        this.fetchWP.get( 'members/count' )
            .then( (json) => this.setState({
				total : typeof json.total !== 'undefined' ? json.total : 0,
				loading : false
            }), (err) => this.setState({ loading : false })
        );
    }

	render() {
		var strings = this.props.hammock.strings;
		return (
			<Dashboard hammock={this.props.hammock}>
				<h2 className="uk-text-center uk-heading-divider">{this.props.hammock.common.string.title}</h2>
				{!this.state.loading && this.state.total > 0 && 
					<React.Fragment>
						<a className="uk-button uk-button-primary uk-button-small" href="#hammock-add-member" uk-toggle="">{strings.dashboard.add_new.button}</a>
						<Filter strings={strings}/>
					</React.Fragment>
				}
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<Table hammock={this.props.hammock} load_counts={this.count_members.bind(this)}/>
				</div>
				<Create hammock={this.props.hammock} />
			</Dashboard>
		)
	}
}

Members.propTypes = {
	hammock: PropTypes.object
};