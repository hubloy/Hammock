import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';


export default class Table extends Component {

	constructor(props) {
		super(props);
		this.state = {
			pager: { current : 0 },
			items: [],
			loading : true,
			error : false,
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
		});

		this.handleRowAction = this.handleRowAction.bind(this);
		this.loadPage = this.loadPage.bind(this);
	}
	
	async componentDidMount() {
		this.loadPage( this.props.page );
	}


	loadPage = async ( page ) => {
		this.fetchWP.get( 'transactions/list?page=' + page )
			.then( (json) => this.setState({
				items : json.items,
				pager : json.pager,
				loading : false,
				error : false,
			}), (err) => this.setState({ loading : false, error : true })
		);
	}
	
	handleRowAction(event, id, action ) {
		event.preventDefault();
		console.log( id + ' ' + action );
		return false;
	}
	
	render() {
		const { pager, items } = this.state;
		var strings = this.props.hubloy_membership.strings;
		if ( this.state.loading) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hubloy_membership.error}</h3>
				)
			} else {
				return (
					<React.Fragment>
						{items.length <= 0 ? (
							<h3 className="uk-text-center uk-margin-top">{strings.dashboard.table.not_found}</h3>
						) : (
							<table className="uk-table uk-background-default">
								<thead>
									<tr>
										<th><input className="uk-checkbox hubloy_membership-top-checkbox" type="checkbox" /></th>
										<th className="uk-width-auto">{strings.dashboard.table.id}</th>
										<th>{strings.dashboard.table.status}</th>
										<th>{strings.dashboard.table.gateway}</th>
										<th>{strings.dashboard.table.amount}</th>
										<th>{strings.dashboard.table.member}</th>
										<th>{strings.dashboard.table.date}</th>
										<th>{strings.dashboard.table.due}</th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th><input className="uk-checkbox hubloy_membership-bottom-checkbox" type="checkbox" /></th>
										<th className="uk-width-auto">{strings.dashboard.table.id}</th>
										<th>{strings.dashboard.table.status}</th>
										<th>{strings.dashboard.table.gateway}</th>
										<th>{strings.dashboard.table.amount}</th>
										<th>{strings.dashboard.table.member}</th>
										<th>{strings.dashboard.table.date}</th>
										<th>{strings.dashboard.table.due}</th>
									</tr>
								</tfoot>
								<tbody>
								{items.map(item =>
									<tr key={item.id}>
										<td><input className="uk-checkbox" type="checkbox" value={item.id} /></td>
										<td>
											{item.invoice_id}
											<div id={"transaction-hover-"+ item.id}>
												<Link uk-tooltip={hubloy_membership.common.buttons.edit} title={hubloy_membership.common.buttons.edit} className="uk-text-primary" to={"/transaction/" + item.id}>{hubloy_membership.common.buttons.edit}</Link>
												{' '}|{' '}
												<a href="#" data-id={item.id} uk-tooltip={hubloy_membership.common.buttons.delete} title={hubloy_membership.common.buttons.delete} className="uk-text-danger" onClick={ e => this.handleRowAction(e, item.id, 'delete')}>{hubloy_membership.common.buttons.delete}</a>
												{item.is_overdue &&
													<React.Fragment>
														{' '}|{' '}<a href="#" data-id={item.id} uk-tooltip={hubloy_membership.common.buttons.reminder} title={hubloy_membership.common.buttons.reminder} className="uk-text-secondary" onClick={e =>  this.handleRowAction(e, item.id, 'remind')}>{hubloy_membership.common.buttons.reminder}</a>
													</React.Fragment>
												}
											</div>
										</td>
										<td>{item.status_name} <span dangerouslySetInnerHTML={{ __html: item.is_overdue ? '<span class="hubloy_membership-text-small uk-label uk-label-warning">' + strings.labels.overdue + '</span>' : ''}}/></td>
										<td>{item.gateway_name}</td>
										<td><span dangerouslySetInnerHTML={{ __html: item.amount_formated }}></span></td>
										<td>
											<a href={item.member_user.edit_url} title={item.member_user.user_info.name} target="_blank">
												{item.member_user.user_info.name}
											</a>
										</td>
										<td>{item.date_created}</td>
										<td>{item.due_date}</td>
									</tr>
								)}
								</tbody>
							</table>
						)}
						<PaginationUI pager={pager} onChange={this.loadPage}/>
					</React.Fragment>
				)
			}
		}
	   
	}
}