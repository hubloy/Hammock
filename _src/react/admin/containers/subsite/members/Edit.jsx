import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import fetchWP from 'utils/fetchWP';
import Dashboard from 'layout/Dashboard';
import {Nav} from './Nav'

import MemberDetail from './view/MemberDetail';
import MemberActivity from './view/MemberActivity';
import MemberTransactions from './view/MemberTransactions';

export default class MemberEdit extends Component {

	constructor(props) {
		super(props);

		this.state = {
			id : this.props.match.params.id,
			loading : true,
			error : false
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	render() {
		
		var self = this,
			hammock = self.props.hammock,
			section = this.props.match.params.section !== undefined ? this.props.match.params.section : 'details';
		return (
			<Dashboard hammock={hammock}>
				<div uk-grid="">
					<LazyLoad className="uk-width-1-4 uk-height-medium">
						<Nav hammock={hammock} active_nav={section} member_id={self.state.id}/>
					</LazyLoad>
					<div className="uk-width-expand uk-margin-left uk-card uk-card-body uk-background-default uk-padding-small">
						{
							{
								'details': <MemberDetail hammock={hammock} id={id} />,
								'activity': <MemberActivity hammock={hammock} id={id}/>,
								'transactions': <MemberTransactions hammock={hammock} id={id}/>
							}[section]
						}
					</div>
				</div>
			</Dashboard>
		)
	}
}
MemberEdit.propTypes = {
	hammock: PropTypes.object
};