<template>
  <div class="resources-view">
    <!-- 页面头部 -->
    <div class="page-header">
      <div class="header-content">
        <h1>📚 学习资源中心</h1>
        <p>发现和分享优质的学习资源，助力你的编程学习之路</p>
      </div>
      <div class="header-actions" v-if="authStore.isAuthenticated">
        <el-button type="primary" @click="showCreateDialog = true">
          <el-icon><Plus /></el-icon>
          添加资源
        </el-button>
      </div>
    </div>

    <!-- 搜索和筛选 -->
    <div class="filters-section">
      <div class="search-box">
        <el-input
          v-model="searchQuery"
          placeholder="搜索资源标题、描述或标签"
          @keyup.enter="handleSearch"
          clearable
        >
          <template #append>
            <el-button @click="handleSearch">
              <el-icon><Search /></el-icon>
            </el-button>
          </template>
        </el-input>
      </div>
      
      <div class="filter-controls">
        <el-select v-model="selectedCategory" placeholder="选择分类" @change="handleSearch" clearable>
          <el-option label="全部分类" value="" />
          <el-option 
            v-for="category in categories" 
            :key="category.id || category" 
            :label="category.name || category" 
            :value="category.id || category"
          />
        </el-select>
        
        <el-select v-model="selectedType" placeholder="资源类型" @change="handleSearch" clearable>
          <el-option label="全部类型" value="" />
          <el-option label="视频教程" value="video" />
          <el-option label="文档资料" value="document" />
          <el-option label="在线链接" value="link" />
          <el-option label="电子书籍" value="book" />
          <el-option label="开发工具" value="tool" />
        </el-select>
        
        <el-select v-model="selectedDifficulty" placeholder="难度等级" @change="handleSearch" clearable>
          <el-option label="全部难度" value="" />
          <el-option label="初级" value="beginner" />
          <el-option label="中级" value="intermediate" />
          <el-option label="高级" value="advanced" />
        </el-select>
        
        <el-switch
          v-model="showFeaturedOnly"
          @change="handleSearch"
          active-text="推荐资源"
          inactive-text="全部资源"
        />
      </div>
    </div>

    <!-- 资源列表 -->
    <div class="resources-content" v-loading="loading">
      <div class="resources-grid" v-if="resources.length > 0">
        <div 
          v-for="resource in resources" 
          :key="resource.id"
          class="resource-card"
        >
          <div class="resource-thumbnail">
            <img v-if="resource.thumbnail" :src="resource.thumbnail" :alt="resource.title" />
            <div v-else class="default-thumbnail">
              <el-icon size="40">
                <component :is="getResourceIcon(resource.type)" />
              </el-icon>
            </div>
            <div class="resource-badges">
              <el-tag v-if="resource.is_featured" type="danger" size="small">推荐</el-tag>
              <el-tag :type="getDifficultyColor(resource.difficulty)" size="small">
                {{ getDifficultyText(resource.difficulty) }}
              </el-tag>
            </div>
          </div>
          
          <div class="resource-content">
            <h3 class="resource-title">{{ resource.title }}</h3>
            <p class="resource-description">{{ resource.description }}</p>
            
            <div class="resource-meta">
              <span class="resource-category">{{ resource.category }}</span>
              <span class="resource-type">{{ getTypeText(resource.type) }}</span>
              <span v-if="resource.duration" class="resource-duration">
                {{ formatDuration(resource.duration) }}
              </span>
            </div>
            
            <div class="resource-stats">
              <span><el-icon><View /></el-icon> {{ resource.view_count || 0 }}</span>
              <span><el-icon><Download /></el-icon> {{ resource.download_count || 0 }}</span>
              <span v-if="resource.rating && Number(resource.rating) > 0"><el-icon><Star /></el-icon> {{ Number(resource.rating).toFixed(1) }}</span>
            </div>
            
            <div class="resource-creator">
              <span>创建者：{{ resource.creator?.real_name || resource.creator?.username }}</span>
              <span>{{ formatDate(resource.created_at) }}</span>
            </div>
          </div>
          
          <div class="resource-actions">
            <el-button @click="viewResource(resource)" size="small">
              查看详情
            </el-button>
            <el-button 
              v-if="resource.url" 
              @click="openResource(resource)" 
              type="primary" 
              size="small"
            >
              访问资源
            </el-button>
            <el-dropdown 
              v-if="canEditResource(resource)" 
              @command="handleResourceAction"
              trigger="click"
            >
              <el-button size="small" text>
                <el-icon><More /></el-icon>
              </el-button>
              <template #dropdown>
                <el-dropdown-menu>
                  <el-dropdown-item :command="{action: 'edit', resource}">编辑</el-dropdown-item>
                  <el-dropdown-item :command="{action: 'delete', resource}" divided>删除</el-dropdown-item>
                </el-dropdown-menu>
              </template>
            </el-dropdown>
          </div>
        </div>
      </div>
      
      <!-- 空状态 -->
      <div v-else class="empty-state">
        <el-icon size="80" color="#909399"><DocumentCopy /></el-icon>
        <p>暂无学习资源</p>
        <el-button v-if="authStore.isAuthenticated" type="primary" @click="showCreateDialog = true">
          添加第一个资源
        </el-button>
      </div>
    </div>

    <!-- 分页 -->
    <div class="pagination-wrapper" v-if="pagination.total > 0">
      <el-pagination
        v-model:current-page="pagination.current_page"
        v-model:page-size="pagination.per_page"
        :total="pagination.total"
        :page-sizes="[12, 24, 48]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSearch"
        @current-change="handleSearch"
      />
    </div>

    <!-- 创建/编辑资源对话框 -->
    <el-dialog
      v-model="showCreateDialog"
      :title="editingResource ? '编辑资源' : '添加新资源'"
      width="60%"
      destroy-on-close
    >
      <ResourceForm
        :resource="editingResource"
        @success="handleResourceSaved"
        @cancel="handleCancelEdit"
      />
    </el-dialog>

    <!-- 资源详情对话框 -->
    <el-dialog
      v-model="showDetailDialog"
      title="资源详情"
      width="70%"
      destroy-on-close
    >
      <ResourceDetail
        v-if="selectedResource"
        :resource="selectedResource"
        @close="showDetailDialog = false"
      />
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { resourceAPI } from '@/api/index'
import { ElMessage, ElMessageBox } from 'element-plus'
import ResourceForm from '@/components/ResourceForm.vue'
import ResourceDetail from '@/components/ResourceDetail.vue'

const authStore = useAuthStore()

// 响应式数据
const loading = ref(false)
const resources = ref([])
const categories = ref([])
const pagination = reactive({
  current_page: 1,
  per_page: 12,
  total: 0
})

// 搜索和筛选
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedType = ref('')
const selectedDifficulty = ref('')
const showFeaturedOnly = ref(false)

// 对话框状态
const showCreateDialog = ref(false)
const showDetailDialog = ref(false)
const editingResource = ref(null)
const selectedResource = ref(null)

// 计算属性
const canEditResource = computed(() => (resource) => {
  if (!authStore.isAuthenticated) return false
  return authStore.isAdmin || resource.creator_id === authStore.user?.id
})

// 方法
const loadResources = async () => {
  loading.value = true
  
  try {
    const params = {
      page: pagination.current_page,
      per_page: pagination.per_page,
      search: searchQuery.value,
      category: selectedCategory.value,
      type: selectedType.value,
      difficulty: selectedDifficulty.value,
      featured: showFeaturedOnly.value
    }

    const response = await resourceAPI.getList(params)
    
    if (response.success) {
      resources.value = response.data.data
      pagination.current_page = response.data.current_page
      pagination.per_page = response.data.per_page
      pagination.total = response.data.total
    } else {
      console.log('❌ API响应失败，使用内置资源')
      loadBuiltInResources()
    }
  } catch (error) {
    console.log('❌ API调用异常，使用内置资源:', error)
    // 加载真实的学习资源数据
    loadBuiltInResources()
  } finally {
    loading.value = false
  }
}

// 加载内置的真实学习资源
const loadBuiltInResources = () => {
  const allResources = [
    // 视频教程类
    {
      id: 1,
      title: '尚硅谷前端技术栈全套教程',
      description: '包含HTML、CSS、JavaScript、Vue、React等前端开发全栈技术，适合初学者系统学习',
      type: 'video',
      category: 'Web开发',
      difficulty: 'beginner',
      url: 'https://www.bilibili.com/video/BV1Kg411T7t9',
      duration: 2400,
      is_featured: true,
      view_count: 158000,
      download_count: 0,
      rating: 4.8,
      creator: { real_name: '尚硅谷', username: 'atguigu' },
      created_at: '2024-01-01',
      thumbnail: 'https://i2.hdslb.com/bfs/archive/fe4c75e4a5e08b000e74a4e24999a0d50a9a18aa.jpg'
    },
    {
      id: 2,
      title: '黑马程序员Java全套教程',
      description: 'Java零基础到就业，包含JavaSE、JavaWeb、框架、项目实战等完整体系',
      type: 'video',
      category: 'Java开发',
      difficulty: 'beginner',
      url: 'https://www.bilibili.com/video/BV1CV411F7dP',
      duration: 3600,
      is_featured: true,
      view_count: 245000,
      download_count: 0,
      rating: 4.9,
      creator: { real_name: '黑马程序员', username: 'itheima' },
      created_at: '2024-01-02'
    },
    {
      id: 3,
      title: 'Vue3 + TypeScript 企业级开发实战',
      description: '基于Vue3和TypeScript的现代前端开发，包含组合式API、Pinia状态管理等',
      type: 'video',
      category: 'Vue开发',
      difficulty: 'intermediate',
      url: 'https://www.bilibili.com/video/BV1dS4y1K7sH',
      duration: 1800,
      is_featured: true,
      view_count: 89000,
      rating: 4.7,
      creator: { real_name: '技术胖', username: 'jspang' },
      created_at: '2024-01-03'
    },
    {
      id: 4,
      title: 'Python数据分析与机器学习',
      description: '从Python基础到数据分析，涵盖pandas、numpy、matplotlib、sklearn等库',
      type: 'video',
      category: 'Python',
      difficulty: 'intermediate',
      url: 'https://www.bilibili.com/video/BV1Ex411d7zK',
      duration: 2100,
      view_count: 76000,
      rating: 4.6,
      creator: { real_name: '小甲鱼', username: 'fishc' },
      created_at: '2024-01-04'
    },

    // 文档资料类
    {
      id: 5,
      title: 'Vue.js 官方中文文档',
      description: 'Vue.js框架的官方中文文档，包含完整的API参考和学习指南',
      type: 'document',
      category: 'Vue开发',
      difficulty: 'beginner',
      url: 'https://cn.vuejs.org/guide/',
      is_featured: true,
      view_count: 120000,
      download_count: 0,
      rating: 4.9,
      creator: { real_name: 'Vue.js官方', username: 'vuejs' },
      created_at: '2024-01-05'
    },
    {
      id: 6,
      title: 'MDN Web 开发文档',
      description: 'Mozilla Developer Network提供的权威Web开发文档，涵盖HTML、CSS、JavaScript',
      type: 'document',
      category: 'Web开发',
      difficulty: 'beginner',
      url: 'https://developer.mozilla.org/zh-CN/',
      is_featured: true,
      view_count: 200000,
      download_count: 0,
      rating: 4.9,
      creator: { real_name: 'Mozilla', username: 'mozilla' },
      created_at: '2024-01-06'
    },
    {
      id: 7,
      title: 'TypeScript 中文文档',
      description: 'TypeScript官方中文文档，学习TypeScript语法和高级特性',
      type: 'document',
      category: 'TypeScript',
      difficulty: 'intermediate',
      url: 'https://www.typescriptlang.org/zh/',
      view_count: 85000,
      rating: 4.8,
      creator: { real_name: 'Microsoft', username: 'microsoft' },
      created_at: '2024-01-07'
    },
    {
      id: 8,
      title: 'Spring Boot 官方文档',
      description: 'Spring Boot框架官方文档，包含配置、开发和部署指南',
      type: 'document',
      category: 'Java开发',
      difficulty: 'intermediate',
      url: 'https://spring.io/projects/spring-boot',
      view_count: 95000,
      rating: 4.7,
      creator: { real_name: 'Spring', username: 'spring' },
      created_at: '2024-01-08'
    },

    // 在线链接类
    {
      id: 9,
      title: 'LeetCode 算法题库',
      description: '全球知名的算法练习平台，提供大量编程题目和在线判题',
      type: 'link',
      category: '算法数据结构',
      difficulty: 'intermediate',
      url: 'https://leetcode.cn/',
      is_featured: true,
      view_count: 180000,
      rating: 4.8,
      creator: { real_name: 'LeetCode', username: 'leetcode' },
      created_at: '2024-01-09'
    },
    {
      id: 10,
      title: 'GitHub - 代码托管平台',
      description: '全球最大的代码托管平台，学习开源项目，参与开源贡献',
      type: 'link',
      category: '开发工具',
      difficulty: 'beginner',
      url: 'https://github.com/',
      is_featured: true,
      view_count: 500000,
      rating: 4.9,
      creator: { real_name: 'GitHub', username: 'github' },
      created_at: '2024-01-10'
    },
    {
      id: 11,
      title: 'CodePen - 在线代码编辑器',
      description: '前端代码在线编辑和分享平台，适合学习前端技术',
      type: 'link',
      category: 'Web开发',
      difficulty: 'beginner',
      url: 'https://codepen.io/',
      view_count: 120000,
      rating: 4.6,
      creator: { real_name: 'CodePen', username: 'codepen' },
      created_at: '2024-01-11'
    },
    {
      id: 12,
      title: '掘金 - 技术社区',
      description: '中国领先的技术社区，分享技术文章和最新技术动态',
      type: 'link',
      category: '技术社区',
      difficulty: 'beginner',
      url: 'https://juejin.cn/',
      view_count: 300000,
      rating: 4.7,
      creator: { real_name: '掘金', username: 'juejin' },
      created_at: '2024-01-12'
    },

    // 电子书籍类
    {
      id: 13,
      title: 'JavaScript 高级程序设计（第4版）',
      description: '前端开发经典教材，深入讲解JavaScript语言特性和Web开发',
      type: 'book',
      category: 'JavaScript',
      difficulty: 'intermediate',
      url: 'https://www.ituring.com.cn/book/2472',
      is_featured: true,
      view_count: 85000,
      download_count: 12000,
      rating: 4.8,
      creator: { real_name: '图灵教育', username: 'ituring' },
      created_at: '2024-01-13'
    },
    {
      id: 14,
      title: 'Java核心技术 卷I',
      description: 'Java开发经典教材，全面介绍Java语言核心特性',
      type: 'book',
      category: 'Java开发',
      difficulty: 'intermediate',
      url: 'https://book.douban.com/subject/26880667/',
      view_count: 95000,
      download_count: 15000,
      rating: 4.9,
      creator: { real_name: '机械工业出版社', username: 'cmpbook' },
      created_at: '2024-01-14'
    },
    {
      id: 15,
      title: '深入理解计算机系统（第3版）',
      description: '计算机系统经典教材，深入理解计算机工作原理',
      type: 'book',
      category: '计算机基础',
      difficulty: 'advanced',
      url: 'https://book.douban.com/subject/26912767/',
      view_count: 65000,
      download_count: 8000,
      rating: 4.8,
      creator: { real_name: '机械工业出版社', username: 'cmpbook' },
      created_at: '2024-01-15'
    },
    {
      id: 16,
      title: 'Python编程：从入门到实践（第2版）',
      description: 'Python入门经典教材，从基础语法到项目实战',
      type: 'book',
      category: 'Python',
      difficulty: 'beginner',
      url: 'https://book.douban.com/subject/35196328/',
      view_count: 110000,
      download_count: 18000,
      rating: 4.7,
      creator: { real_name: '人民邮电出版社', username: 'ptpress' },
      created_at: '2024-01-16'
    },

    // 开发工具类
    {
      id: 17,
      title: 'Visual Studio Code',
      description: '微软开发的免费代码编辑器，支持多种编程语言和丰富插件',
      type: 'tool',
      category: '开发工具',
      difficulty: 'beginner',
      url: 'https://code.visualstudio.com/',
      is_featured: true,
      view_count: 800000,
      download_count: 500000,
      rating: 4.9,
      creator: { real_name: 'Microsoft', username: 'microsoft' },
      created_at: '2024-01-17'
    },
    {
      id: 18,
      title: 'Git - 版本控制系统',
      description: '分布式版本控制系统，程序员必备工具',
      type: 'tool',
      category: '开发工具',
      difficulty: 'beginner',
      url: 'https://git-scm.com/',
      is_featured: true,
      view_count: 400000,
      download_count: 200000,
      rating: 4.8,
      creator: { real_name: 'Git', username: 'git' },
      created_at: '2024-01-18'
    },
    {
      id: 19,
      title: 'Node.js 运行环境',
      description: '基于Chrome V8引擎的JavaScript运行环境，支持服务端开发',
      type: 'tool',
      category: 'Node.js',
      difficulty: 'intermediate',
      url: 'https://nodejs.org/',
      view_count: 300000,
      download_count: 150000,
      rating: 4.7,
      creator: { real_name: 'Node.js', username: 'nodejs' },
      created_at: '2024-01-19'
    },
    {
      id: 20,
      title: 'Postman API 测试工具',
      description: 'API开发和测试工具，支持REST、GraphQL等多种API格式',
      type: 'tool',
      category: '开发工具',
      difficulty: 'beginner',
      url: 'https://www.postman.com/',
      view_count: 250000,
      download_count: 100000,
      rating: 4.6,
      creator: { real_name: 'Postman', username: 'postman' },
      created_at: '2024-01-20'
    },

    // 更多视频教程
    {
      id: 21,
      title: 'React18 + TypeScript 开发教程',
      description: 'React18新特性详解，结合TypeScript进行企业级前端开发',
      type: 'video',
      category: 'React开发',
      difficulty: 'intermediate',
      url: 'https://www.bilibili.com/video/BV1dV4y1u7hJ',
      duration: 1500,
      is_featured: true,
      view_count: 125000,
      rating: 4.8,
      creator: { real_name: '尚硅谷', username: 'atguigu' },
      created_at: '2024-01-21'
    },
    {
      id: 22,
      title: 'Spring Cloud 微服务架构',
      description: 'Spring Cloud全家桶，构建微服务架构系统',
      type: 'video',
      category: 'Java开发',
      difficulty: 'advanced',
      url: 'https://www.bilibili.com/video/BV1M14y1k7qM',
      duration: 2800,
      view_count: 95000,
      rating: 4.7,
      creator: { real_name: '黑马程序员', username: 'itheima' },
      created_at: '2024-01-22'
    },
    {
      id: 23,
      title: 'MySQL 数据库从入门到精通',
      description: '数据库基础、SQL语法、索引优化、性能调优完整教程',
      type: 'video',
      category: '数据库',
      difficulty: 'intermediate',
      url: 'https://www.bilibili.com/video/BV1kr4y1i7ru',
      duration: 3200,
      is_featured: true,
      view_count: 180000,
      rating: 4.9,
      creator: { real_name: '尚硅谷', username: 'atguigu' },
      created_at: '2024-01-23'
    },
    {
      id: 24,
      title: 'Docker 容器技术教程',
      description: 'Docker容器化部署，从基础到实战项目应用',
      type: 'video',
      category: '运维部署',
      difficulty: 'intermediate',
      url: 'https://www.bilibili.com/video/BV1gr4y1U7CY',
      duration: 1800,
      view_count: 110000,
      rating: 4.6,
      creator: { real_name: '狂神说Java', username: 'kuangshen' },
      created_at: '2024-01-24'
    },

    // 更多文档资料
    {
      id: 25,
      title: 'React 官方中文文档',
      description: 'React框架官方文档，学习组件化开发和状态管理',
      type: 'document',
      category: 'React开发',
      difficulty: 'beginner',
      url: 'https://react.docschina.org/',
      is_featured: true,
      view_count: 150000,
      rating: 4.8,
      creator: { real_name: 'React官方', username: 'react' },
      created_at: '2024-01-25'
    },
    {
      id: 26,
      title: 'Vite 构建工具文档',
      description: '新一代前端构建工具，快速的开发体验',
      type: 'document',
      category: '构建工具',
      difficulty: 'intermediate',
      url: 'https://cn.vitejs.dev/',
      view_count: 85000,
      rating: 4.7,
      creator: { real_name: 'Vite团队', username: 'vitejs' },
      created_at: '2024-01-26'
    },
    {
      id: 27,
      title: 'Element Plus 组件库',
      description: '基于Vue3的桌面端组件库，提供丰富的UI组件',
      type: 'document',
      category: 'UI组件',
      difficulty: 'beginner',
      url: 'https://element-plus.org/zh-CN/',
      view_count: 200000,
      rating: 4.8,
      creator: { real_name: 'Element团队', username: 'element' },
      created_at: '2024-01-27'
    },

    // 更多在线工具和平台
    {
      id: 28,
      title: '牛客网 - 编程面试练习',
      description: '专注于编程面试的在线平台，算法题和面试经验分享',
      type: 'link',
      category: '算法数据结构',
      difficulty: 'intermediate',
      url: 'https://www.nowcoder.com/',
      view_count: 280000,
      rating: 4.7,
      creator: { real_name: '牛客网', username: 'nowcoder' },
      created_at: '2024-01-28'
    },
    {
      id: 29,
      title: 'Stack Overflow 中文版',
      description: '程序员问答社区，解决编程技术问题',
      type: 'link',
      category: '技术社区',
      difficulty: 'beginner',
      url: 'https://stackoverflow.com/',
      view_count: 400000,
      rating: 4.9,
      creator: { real_name: 'Stack Overflow', username: 'stackoverflow' },
      created_at: '2024-01-29'
    },
    {
      id: 30,
      title: 'Can I Use - 浏览器兼容性查询',
      description: '查询CSS、JS、HTML5等技术的浏览器支持情况',
      type: 'link',
      category: 'Web开发',
      difficulty: 'beginner',
      url: 'https://caniuse.com/',
      view_count: 150000,
      rating: 4.8,
      creator: { real_name: 'Can I Use', username: 'caniuse' },
      created_at: '2024-01-30'
    },

    // 更多电子书籍
    {
      id: 31,
      title: '算法导论（第3版）',
      description: '计算机算法经典教材，深入理解算法设计与分析',
      type: 'book',
      category: '算法数据结构',
      difficulty: 'advanced',
      url: 'https://book.douban.com/subject/20432061/',
      view_count: 85000,
      download_count: 10000,
      rating: 4.8,
      creator: { real_name: '机械工业出版社', username: 'cmpbook' },
      created_at: '2024-01-31'
    },
    {
      id: 32,
      title: 'Vue.js 设计与实现',
      description: '深入Vue.js源码，理解框架设计原理和实现细节',
      type: 'book',
      category: 'Vue开发',
      difficulty: 'advanced',
      url: 'https://book.douban.com/subject/35768338/',
      view_count: 65000,
      download_count: 8000,
      rating: 4.9,
      creator: { real_name: '人民邮电出版社', username: 'ptpress' },
      created_at: '2024-02-01'
    },

    // 更多开发工具
    {
      id: 33,
      title: 'WebStorm IDE',
      description: 'JetBrains出品的JavaScript开发IDE，智能代码编辑',
      type: 'tool',
      category: '开发工具',
      difficulty: 'beginner',
      url: 'https://www.jetbrains.com/webstorm/',
      view_count: 120000,
      download_count: 50000,
      rating: 4.7,
      creator: { real_name: 'JetBrains', username: 'jetbrains' },
      created_at: '2024-02-02'
    },
    {
      id: 34,
      title: 'Figma 设计工具',
      description: '在线协作设计工具，UI/UX设计和原型制作',
      type: 'tool',
      category: '设计工具',
      difficulty: 'beginner',
      url: 'https://www.figma.com/',
      view_count: 180000,
      download_count: 80000,
      rating: 4.8,
      creator: { real_name: 'Figma', username: 'figma' },
      created_at: '2024-02-03'
    },
    {
      id: 35,
      title: 'Redis 数据库',
      description: '高性能的内存数据库，支持缓存、消息队列等场景',
      type: 'tool',
      category: '数据库',
      difficulty: 'intermediate',
      url: 'https://redis.io/',
      view_count: 140000,
      download_count: 60000,
      rating: 4.8,
      creator: { real_name: 'Redis', username: 'redis' },
      created_at: '2024-02-04'
    }
  ]

  // 应用筛选条件
  let filteredResources = allResources

  // 搜索筛选
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filteredResources = filteredResources.filter(resource =>
      resource.title.toLowerCase().includes(query) ||
      resource.description.toLowerCase().includes(query) ||
      resource.category.toLowerCase().includes(query)
    )
  }

  // 分类筛选
  if (selectedCategory.value) {
    filteredResources = filteredResources.filter(resource =>
      resource.category === selectedCategory.value
    )
  }

  // 类型筛选
  if (selectedType.value) {
    filteredResources = filteredResources.filter(resource =>
      resource.type === selectedType.value
    )
  }

  // 难度筛选
  if (selectedDifficulty.value) {
    filteredResources = filteredResources.filter(resource =>
      resource.difficulty === selectedDifficulty.value
    )
  }

  // 推荐筛选
  if (showFeaturedOnly.value) {
    filteredResources = filteredResources.filter(resource => resource.is_featured)
  }

  // 分页处理
  const startIndex = (pagination.current_page - 1) * pagination.per_page
  const endIndex = startIndex + pagination.per_page
  
  resources.value = filteredResources.slice(startIndex, endIndex)
  pagination.total = filteredResources.length
}

const loadCategories = async () => {
  try {
    const response = await resourceAPI.getCategories()
    if (response.success) {
      categories.value = response.data.categories
    }
  } catch (error) {
    console.log('使用内置分类数据')
    // 提供内置分类数据
    categories.value = [
      'Web开发',
      'Java开发', 
      'Vue开发',
      'React开发',
      'Python',
      'TypeScript',
      'JavaScript',
      'Node.js',
      '算法数据结构',
      '开发工具',
      '技术社区',
      '计算机基础',
      '数据库',
      '运维部署',
      '构建工具',
      'UI组件',
      '设计工具'
    ]
  }
}

const handleSearch = () => {
  pagination.current_page = 1
  loadResources()
}

const viewResource = async (resource) => {
  try {
    const response = await resourceAPI.getById(resource.id)
    if (response.success) {
      selectedResource.value = response.data.resource
      showDetailDialog.value = true
    }
  } catch (error) {
    // 直接使用传入的资源对象作为详情数据
    selectedResource.value = resource
    showDetailDialog.value = true
  }
}

const openResource = (resource) => {
  if (resource.url) {
    window.open(resource.url, '_blank')
  }
}

const handleResourceAction = async ({ action, resource }) => {
  switch (action) {
    case 'edit':
      editingResource.value = resource
      showCreateDialog.value = true
      break
    case 'delete':
      await deleteResource(resource)
      break
  }
}

const deleteResource = async (resource) => {
  try {
    await ElMessageBox.confirm(
      `确定要删除资源"${resource.title}"吗？`,
      '确认删除',
      {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }
    )

    await resourceAPI.delete(resource.id)
    ElMessage.success('资源删除成功')
    loadResources()
  } catch (error) {
    if (error !== 'cancel') {
      console.error('删除资源失败:', error)
    }
  }
}

const handleResourceSaved = () => {
  showCreateDialog.value = false
  editingResource.value = null
  loadResources()
}

const handleCancelEdit = () => {
  showCreateDialog.value = false
  editingResource.value = null
}

// 辅助方法
const getResourceIcon = (type) => {
  const icons = {
    video: 'VideoPlay',
    document: 'Document',
    link: 'Link',
    book: 'Reading',
    tool: 'Box'
  }
  return icons[type] || 'Document'
}

const getDifficultyColor = (difficulty) => {
  const colors = {
    beginner: 'success',
    intermediate: 'warning',
    advanced: 'danger'
  }
  return colors[difficulty] || 'info'
}

const getDifficultyText = (difficulty) => {
  const texts = {
    beginner: '初级',
    intermediate: '中级',
    advanced: '高级'
  }
  return texts[difficulty] || difficulty
}

const getTypeText = (type) => {
  const texts = {
    video: '视频教程',
    document: '文档资料',
    link: '在线链接',
    book: '电子书籍',
    tool: '开发工具'
  }
  return texts[type] || type
}

const formatDuration = (minutes) => {
  if (minutes < 60) {
    return `${minutes}分钟`
  }
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return `${hours}小时${mins > 0 ? mins + '分钟' : ''}`
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('zh-CN')
}

// 生命周期
onMounted(() => {
  loadResources()
  loadCategories()
})
</script>

<style scoped>
.resources-view {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e4e7ed;
}

.header-content h1 {
  margin: 0 0 8px 0;
  color: #2c3e50;
  font-size: 2rem;
}

.header-content p {
  margin: 0;
  color: #606266;
  font-size: 1rem;
}

.filters-section {
  margin-bottom: 30px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.search-box {
  margin-bottom: 20px;
}

.search-box .el-input {
  max-width: 400px;
}

.filter-controls {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  align-items: center;
}

.filter-controls .el-select {
  min-width: 120px;
}

.resources-content {
  margin-bottom: 30px;
}

.resources-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.resource-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  position: relative;
}

.resource-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.resource-thumbnail {
  position: relative;
  height: 180px;
  overflow: hidden;
}

.resource-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.default-thumbnail {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
}

.resource-badges {
  position: absolute;
  top: 10px;
  right: 10px;
  display: flex;
  gap: 5px;
  flex-direction: column;
}

.resource-content {
  padding: 20px;
}

.resource-title {
  margin: 0 0 10px 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.resource-description {
  margin: 0 0 15px 0;
  color: #606266;
  font-size: 0.9rem;
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.resource-meta {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
  font-size: 0.8rem;
  color: #909399;
}

.resource-stats {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
  font-size: 0.8rem;
  color: #909399;
}

.resource-stats span {
  display: flex;
  align-items: center;
  gap: 4px;
}

.resource-creator {
  display: flex;
  justify-content: space-between;
  font-size: 0.8rem;
  color: #909399;
  margin-bottom: 15px;
}

.resource-actions {
  display: flex;
  gap: 8px;
  justify-content: space-between;
  align-items: center;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #909399;
}

.empty-state p {
  margin: 20px 0;
  font-size: 1.1rem;
}

.pagination-wrapper {
  display: flex;
  justify-content: center;
  padding: 20px 0;
}

/* 移动端适配 */
@media (max-width: 768px) {
  .resources-view {
    padding: 15px;
  }
  
  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 15px;
  }
  
  .header-content h1 {
    font-size: 1.5rem;
  }
  
  .filter-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-controls .el-select {
    min-width: auto;
  }
  
  .resources-grid {
    grid-template-columns: 1fr;
  }
  
  .resource-actions {
    flex-direction: column;
    gap: 8px;
  }
}
</style> 