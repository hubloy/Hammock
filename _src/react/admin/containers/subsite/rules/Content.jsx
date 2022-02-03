import React, { Component } from 'react';
import fetchWP from 'utils/fetchWP';
import {PaginationUI} from 'ui/admin/form';


export default class Table extends Component {

    constructor(props) {
		super(props);
		this.state = {
			pager: { current : 1, total : 0, pages : [] },
			items: [],
			loading : true,
			error : false,
		};
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
		});

		this.getData = this.getData.bind(this);
	}

	getData = async ( page ) => {
		var type = this.props.type;
		this.fetchWP.get( 'rules/get/' + type + '?page=' + page )
			.then( (json) => {
				this.setState({
					items : json.items,
					pager : json.pager,
					loading : false,
					error : ! json.success,
				});
				window.hammock.helper.select2();
			}, (err) => this.setState({ loading : false, error : true })
		);
	}

	async componentDidMount() {
		this.getData( this.props.page );
	}

	async componentDidUpdate( prevProps ) {
		if ( this.props.type !== prevProps.type ) {
			this.setState({ loading : true, error : false });
			this.getData( this.props.page );
		}
	}

	handleRowAction(event, id, action ) {
		event.preventDefault();
		console.log( id + ' ' + action );
		return false;
	}

    render() {
		const { pager, items, loading, error } = this.state;
        var hammock = this.props.hammock,
		    strings = hammock.strings;
		return (
			<React.Fragment>
				{loading ? (
					<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
						<span className="uk-text-center" uk-spinner="ratio: 3"></span>
					</div>
				) : (
					error ? (
						<h3 className="uk-text-center uk-margin-top">{hammock.error}</h3>
					) : (
						pager.total <= 0 ? (
							<h3 className="uk-text-center uk-margin-top">{hammock.no_data}</h3>
						) : (
							<table className="uk-table uk-background-default">
								<thead>
									<tr>
										<th><input className="uk-checkbox hammock-top-checkbox" type="checkbox" /></th>
										<th className="uk-width-auto">{strings.dashboard.table.id}</th>
										<th>{strings.dashboard.table.desc}</th>
										<th>{strings.dashboard.table.status}</th>
										<th>{strings.dashboard.table.type}</th>
										<th>{strings.dashboard.table.date}</th>
									</tr>
								</thead>
								<tbody>
									{items.map(item =>
										<tr key={item.rule_id}>
											<td><input className="uk-checkbox" type="checkbox" value={item.rule_id} /></td>
											<td>
												{item.rule_id}
												<div id={"rule-hover-"+ item.rule_id}>
													<a uk-tooltip={hammock.common.buttons.edit} title={hammock.common.buttons.edit} className="uk-text-primary">{hammock.common.buttons.edit}</a>
													{' '}|{' '}
													<a href="#" data-id={item.rule_id} uk-tooltip={hammock.common.buttons.delete} title={hammock.common.buttons.delete} className="uk-text-danger" onClick={ e => this.handleRowAction(e, item.id, 'delete')}>{hammock.common.buttons.delete}</a>
												</div>
											</td>
											<td>{item.status_name} <span dangerouslySetInnerHTML={{ __html: item.is_overdue ? '<span class="hammock-text-small uk-label uk-label-warning">' + strings.labels.overdue + '</span>' : ''}}/></td>
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
								<tfoot>
									<tr>
										<th><input className="uk-checkbox hammock-top-checkbox" type="checkbox" /></th>
										<th className="uk-width-auto">{strings.dashboard.table.id}</th>
										<th>{strings.dashboard.table.desc}</th>
										<th>{strings.dashboard.table.status}</th>
										<th>{strings.dashboard.table.type}</th>
										<th>{strings.dashboard.table.date}</th>
									</tr>
								</tfoot>
							</table>
						)
					)
				)}
				<PaginationUI pager={pager} onChange={this.getData} base={this.props.type}/>
			</React.Fragment>
		)
    }
}