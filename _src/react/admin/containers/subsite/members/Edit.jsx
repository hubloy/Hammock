import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

import fetchWP from 'utils/fetchWP';
import Dashboard from 'layout/Dashboard';
import {Nav} from './view/Nav'

import MemberDetail from './view/MemberDetail';
import MemberActivity from './view/MemberActivity';
import MemberSubscriptions from './view/MemberSubscriptions';
import MemberTransactions from './view/MemberTransactions';

export default class MemberEdit extends Component {

	constructor(props) {
		super(props);

		this.state = {
			member : {},
			id : this.props.match.params.id,
			loading : true,
			error : false
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	async componentDidMount() {
		this.load_member();
	}

	load_member = async () => {
		const id = this.state.id;
		this.fetchWP.get( 'members/get?id=' + id )
			.then( (json) => this.setState({
				member : json,
				loading : false,
				error : false,
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( self.props.hubloy_membership.error, 'error' );
			}
		);
	}

	render() {
		
		var self = this,
			hubloy_membership = self.props.hubloy_membership,
			section = this.props.match.params.section !== undefined ? this.props.match.params.section : 'subs',
			id = self.state.id,
			strings = hubloy_membership.strings,
			member = this.state.member;
		return (
			<Dashboard hubloy_membership={hubloy_membership}>
				{this.state.loading ? (
					<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
						<span className="uk-text-center" uk-spinner="ratio: 3"></span>
					</div>
				) : (
					this.state.member.id <= 0 ? (
						<div className="uk-container uk-text-center uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
							<h3 className="uk-text-center">{strings.edit.not_found}</h3>
							<Link className="uk-border-rounded uk-margin-bottom uk-background-default uk-button uk-button-default uk-button-small" to="/">{strings.edit.back}</Link>
						</div>
					) : (
						<div uk-grid="">
							<div className="uk-width-1-4 uk-height-medium">
								<MemberDetail hubloy_membership={hubloy_membership} id={id} member={member} />
							</div>
							<div className="uk-width-expand uk-margin-left uk-card uk-card-body uk-background-default uk-padding-small">
								<Nav active_nav={section} hubloy_membership={hubloy_membership} member_id={id} />
								{
									{
										'subs': <MemberSubscriptions hubloy_membership={hubloy_membership} id={id} member={member} />,
										'activity': <MemberActivity hubloy_membership={hubloy_membership} id={id} member={member}/>,
										'transactions': <MemberTransactions hubloy_membership={hubloy_membership} id={id} member={member}/>
									}[section]
								}
							</div>
						</div>
					)
				)}
				
			</Dashboard>
		)
	}
}
MemberEdit.propTypes = {
	hubloy_membership: PropTypes.object
};