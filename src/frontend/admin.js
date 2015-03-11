/**
 * Engage-360D Sberbank Depository Frontend
 */

// Localization
var i18n = require("attreactive-i18n");
i18n.locale('ru');

// Rendering dependencies
var Promise = require('bluebird');
var React = require("react");
var Admin = require("engage-360d-admin/components/Admin");
var $ = require("jquery");

// Configuration dependencies
var AuthorizationManager = require("attreactive-auth/lib/AuthorizationManager");
var Router = require("attreactive-router/lib/Router");
var menuFactory = require("attreactive-menu/lib/menuFactory");
var AdminSetup = require("attreactive-admin/lib/AdminSetup");
var FormComponentMatcher = require("attreactive-admin/lib/FormComponentMatcher");
var FormatterFactory = require("attreactive-admin/lib/formatter/FormatterFactory");
var AdminResourceFactory = require("attreactive-admin/lib/resource/AdminResourceFactory");
var AdminResource = require("attreactive-admin/lib/resource/AdminResource");
var AdminResourceProperty = require("attreactive-admin/lib/resource/AdminResourceProperty");
var RestStorage = require("attreactive-admin/lib/storage/RestStorage");
var PropertyStringifier = require("attreactive-admin/lib/stringifier/PropertyStringifier");
var validationConstraints = require("attreactive-validator/lib/validationConstraints");
var PageBlockRegistry = require("engage-360d-admin/PageBlockRegistry");
var apiRequest = require("./utilities/apiRequest");

// Services
var authManager = new AuthorizationManager(
  function (credentials) {
    return Promise.resolve(apiRequest('POST', '/api/v1/tokens', {
      email: credentials._username,
      plainPassword: credentials._password
    }, function () {}))
      .then(function(output) {
        var token = output.data.id;
        var user = output.linked.users[0];
        return {
          userFullName: [user.firstname, user.lastname].join(' '),
          token: token
        };
      });
  }
);
var router = new Router();
var adminSetup = new AdminSetup(authManager, router);
var formComponentMatcher = new FormComponentMatcher();
var formatterFactory = new FormatterFactory();
var adminResourceFactory = new AdminResourceFactory(adminSetup, formComponentMatcher, formatterFactory);
var pageBlockRegistry = new PageBlockRegistry();

// Default CRUD components
adminResourceFactory.setDefaultListingComponent(require("engage-360d-admin/components/crud/CrudList"));
adminResourceFactory.setDefaultViewComponent(require("engage-360d-admin/components/crud/CrudView"));
adminResourceFactory.setDefaultFormComponent(require("engage-360d-admin/components/crud/CrudForm"));

// Form fields
formComponentMatcher.registerComponent('input', require("engage-360d-admin/components/form/Input"));
formComponentMatcher.registerComponent('checkbox', require("engage-360d-admin/components/form/Checkbox"));
formComponentMatcher.registerComponent('password', require("engage-360d-admin/components/form/PasswordInput"));
formComponentMatcher.registerComponent('file', require("engage-360d-admin/components/form/FileInput"));
formComponentMatcher.registerComponent('many-to-many-select', require("engage-360d-admin/components/form/ManyToManySelect"));
formComponentMatcher.registerComponent('ckeditor', require("engage-360d-admin/components/form/CKEditor"));
formComponentMatcher.registerComponent('date-time-picker', require("engage-360d-admin/components/form/DateTimePicker"));

// Page Blocks
pageBlockRegistry.addPageBlockComponent({
  type: 'sonata.block.service.text',
  title: 'Текстовый блок',
  initialJSON: JSON.stringify({content: ''}),
  component: require("engage-360d-admin/components/pageBlocks/TextPageBlock")
});

pageBlockRegistry.addPageBlockComponent({
  type: 'engage360d_takeda.block.map',
  title: 'Карта',
  initialJSON: JSON.stringify({}),
  component: require("./admin/MapBlock")
});

// Formatters
formatterFactory.factory('string', require("attreactive-admin/lib/formatter/StringFormatter"));
formatterFactory.factory('number', require("attreactive-admin/lib/formatter/NumberFormatter"));
formatterFactory.factory('date', require("attreactive-admin/lib/formatter/DateFormatter"));
formatterFactory.factory('boolean', require("engage-360d-admin/lib/admin/formatter/BooleanFormatter"));
formatterFactory.factory('many-to-many', require("attreactive-admin/lib/formatter/ManyToManyFormatter"));
formatterFactory.factory('many-to-one', require("attreactive-admin/lib/formatter/ManyToOneFormatter"));

router.add('/', function() {
  window.location.href = '#!/pages/';
});

// Menu
var menu = [
  {title: 'Содержимое сайта', menu: menuFactory([
    {url: '/pages/', title: 'Страницы', icon: 'file-text-o'}
  ])}
];

$(document).ajaxComplete(function(event, jqXHR, ajaxOptions) {
  if (jqXHR.status == 401) {
    localStorage.removeItem('attreactive-auth/payload');
    window.location.href = window.location.pathname;
  }
});

// Pages
adminResourceFactory.factory('pages', {
  title: 'Страницы',
  rest: {
    baseUrl: '/api/old/pages',
  },
  meta: {
    icon: 'file-text-o'
  },
  stringify: 'title',
  listingComponent: require("engage-360d-admin/components/page/PageTree"),
  formComponent: function(props) {
    props.pageBlockRegistry = pageBlockRegistry;
    return require("engage-360d-admin/components/page/PageForm")(props);
  },
  properties: {
    url: {
      title: 'Ссылка на страницу',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Ссылка на страницу не может быть пустой'
      }
    },
    title: {
      title: 'Заголовок',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Заголовок не может быть пустым'
      }
    },
    description: {
      title: 'Описание для SEO',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Описание для SEO не может быть пустым'
      }
    },
    keywords: {
      title: 'Ключевые слова для SEO',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Ключевые слова для SEO не может быть пустым'
      }
    },
    isActive: {
      title: 'Страница опубликована',
      component: 'checkbox',
      format: 'boolean'
    },
    pageBlocks: {
      visibleInView: false,
      defaultValue: []
    }
  }
});

// Rendering
$(function() {
  React.renderComponent(
    Admin({
      title: 'Такеда. Кардиомагнил',
      copyright: '© 2015 Такеда. Кардиомагнил',
      creatorName: 'Engage',
      creatorUrl: 'http://iengage.ru',
      signUpEmail: 'it@iengage.ru',
      adminSetup: adminSetup,
      menu: menu
    }),
    document.getElementById('Admin')
  );
});
