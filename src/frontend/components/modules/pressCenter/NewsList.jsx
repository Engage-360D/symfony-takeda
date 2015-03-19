/**
 * cardiomagnyl
 */

var React = require('react');
var truncate = require('html-truncate');
var moment = require('moment');
var DateInput = require('../../form/DateInput');

var NewsList = React.createClass({
  getInitialState: function() {
    return {
      filterByDate: '',
      filterByCategory: '',
      page: 1
    };
  },

  getDefaultProps: function() {
    return {
      articlesPerPage: 4,
      paginationVisiblePageNum: 4
    };
  },

  setFilteringByDate: function(date, e) {
    e.preventDefault();
    this.setState({filterByDate: date});
  },

  resetFilteringByDate: function(e) {
    e.preventDefault();
    this.setState({filterByDate: ''});
  },

  setFilteringByCategory: function(category, e) {
    e.preventDefault();
    this.setState({filterByCategory: category});
  },

  resetFilteringByCategory: function(e) {
    e.preventDefault();
    this.setState({filterByCategory: ''});
  },

  getPageNum: function(articles) {
    return Math.ceil(articles.length / this.props.articlesPerPage);
  },

  getLinkedCategory: function(article) {
    // TODO ajax category
    return article.links.category;
  },

  renderArticle: function(article) {
    var category = this.getLinkedCategory(article);

    return (
      <a className="post__cell" href={article.uri}>
        <div className="post__meta">
          <span className="post__date">{moment(article.createdAt).format("DD.MM.YYYY")}</span>
          <span className={"post__type " + category.keyword}>{category.data}</span>
        </div>
        <div className="post__title">{article.title}</div>
        <div className="post__text" dangerouslySetInnerHTML={{__html: truncate(article.content, 200)}} />
      </a>
    );
  },

  renderPager: function(articles) {
    var pageNum = this.getPageNum(articles);
    var currPage = this.state.page;
    var visibleNum = this.props.paginationVisiblePageNum;
    var links = [];
    var start = Math.floor((currPage - 1)/ visibleNum) * visibleNum + 1;
    var i;

    if (!pageNum) {
      return <div />
    }

    if (start > pageNum) {
      start = pageNum - visibleNum;
    }

    if (start > visibleNum) {
      links.push(<a href="#" onClick={this.showPage.bind(this, start - 1)}>...</a>);
    }

    for (i = start; i < start + visibleNum && i <= pageNum; i++) {
      links.push(
        <a href="#"
        onClick={this.showPage.bind(this, i)}
        className={i === currPage ? "is-active" : ""}>{i}</a>
      )
    }

    if (i <= pageNum) {
      links.push(<a href="#" onClick={this.showPage.bind(this, i)}>...</a>);
    }

    return (<div className="pager">{links}</div>);
  },

  showPage: function(page, e) {
    e.preventDefault();
    this.setState({
      page: page
    });
  },

  showPrevPage: function() {
    this.setState({
      page: Math.max(this.state.page - 1, 1)
    });
  },

  showNextPage: function(articles) {
    this.setState({
      page: Math.min(this.state.page + 1, this.getPageNum(articles))
    });
  },

  renderCategoryFilter: function() {
    // TODO refactor, use ajax
    var categories = ["лечение", "профилактика"];

    return (
      <div className="sorter">
        <a href="#"
          onClick={this.resetFilteringByCategory}
          className={!this.state.filterByCategory ? "is-active" : ""}>все</a>
        {categories.map(function(category) {
            return (
              <a href="#"
                onClick={this.setFilteringByCategory.bind(this, category)}
                className={category === this.state.filterByCategory ? "is-active" : ""}>{category}</a>
            );
        }.bind(this))}
      </div>
    );
  },

  render: function() {
    // initially articles are sorted by createdAt field
    var articles = this.props.articles.slice();
    var firstRow = [];
    var secondRow = [];
    var i;
    var nextPageButton = <div/>;
    var prevPageButton = <div />;
    var dateLink = {
        value: this.state.filterByDate,
        requestChange: function(newDate) {
          this.setState({filterByDate: newDate});
        }.bind(this)
    };

    if (this.state.filterByCategory) {
      articles = articles.filter(function(article) {
        return this.getLinkedCategory(article).data.toLowerCase() === this.state.filterByCategory;
      }.bind(this));
    }

    if (this.state.filterByDate) {
      articles = articles.filter(function(article) {
        return moment(this.state.filterByDate).isSame(moment(article.createdAt), 'day')
      }.bind(this));
    }

    for (i = (this.state.page - 1) * this.props.articlesPerPage; i < articles.length; i++) {
      if (i % 2 === 0) {
        firstRow.push(articles[i]);
      } else {
        secondRow.push(articles[i]);
      }
    }

    if (this.state.page < this.getPageNum(articles)) {
      nextPageButton = (
        <button className="post__next" onClick={this.showNextPage.bind(this, articles)}>
          <i className="icon icon-arr-right"></i>
        </button>
      );
    }

    if (this.state.page > 1) {
      prevPageButton = (
        <button className="post__prev" onClick={this.showPrevPage}>
          <i className="icon icon-arr-left"></i>
        </button>
      );
    }

    return (
      <div>
        <div className="h-wrap">
          <div className="h">Новости</div>
          {this.renderCategoryFilter()}
          <DateInput valueLink={dateLink} />
        </div>
        <div className="post">
          {nextPageButton}
          {prevPageButton}
          <div className="post__table">
            <div className="post__row">{firstRow.map(this.renderArticle)}</div>
            <div className="post__row post__row_down">{secondRow.map(this.renderArticle)}</div>
          </div>
        </div>
        {this.renderPager.call(this, articles)}
      </div>
    );
  }
});

module.exports = NewsList;
